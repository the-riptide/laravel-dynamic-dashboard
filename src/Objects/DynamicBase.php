<?php 

namespace TheRiptide\LaravelDynamicDashboard\Objects;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use TheRiptide\LaravelDynamicDashboard\Traits\Types;
use TheRiptide\LaravelDynamicDashboard\Models\DynHead;

class DynamicBase {

    use Types;

    public $slug;
    public $user_id;
    public $id;
    
    private $models;
    private $dynHead;
    private $canDelete = true;

    private $modelPath = 'TheRiptide\LaravelDynamicDashboard\Models\Dyn';
    
    public function __construct(DynHead|string $head = null) {

        $head
            ? $this->prepare($head)
            : $this->getNewModels();
    }

    public function canDelete()
    {
        return $this->canDelete;
    }
    
    public function tableHeads() : Collection
    {
        return $this->index()->map(
            function($item, $key) {

                if (is_array($item) || $item instanceof Collection) return $key;
                
                return $item;
            }
        )->filter();
    }

    public function setValue($field) : string
    {
        if (isset($this->index()[$field]['function']) && $this->index()[$field]['function']) 
        {
            return $this->$field();
        } 

        return $this->$field;
    }

    public function models()
    {
        return $this->models;
    }

    public function head()
    {
        return $this->dynHead;
    }

    public function rules()
    {
        return $this->models
            ->filter(
                function ($item) {

                    return $item->rules;
                })->mapWithKeys(function ($item) {
                    if ($item->rules) {
                        
                        if (is_array($item->rules)) $rules = $item->exists() ? $item->rules[0] : $item->rules[1];    
                        else $rules = $item->rules;
                     
                        return [$item->name => $rules];
                    }
                }
            );
    }

    private function prepare($head)
    {
        if (is_string($head)) $head = DynHead::firstWhere('slug', $head);
        $this->models = $head->getAll();
        $this->dynHead = $this->models->shift();
        $this->prepHead($this->dynHead);
        $this->prepFields($this->models);
    }

    private function prepHead($head) {

        Collect(Schema::getColumnListing($head->getTable()))->map(
            function ($field) use ($head) {
                if (! Str::startswith($field, 'next')) {
                    $this->$field = $head->$field;
                }        
            }
        );
    }

    private function prepFields($objects) {

        $objects->map(fn ($item) => $this->{$item->name} = $item->content);
    }


    public function index() 
    {
        return $this->fields()->map(fn ($attribute, $key) => $key);
    }

    public function fields() 
    {
        return collect([]);
    }

    public function save($contents) 
    {
        $this->previous = $this->dynHead;
        
        $this->models->map(
            function ($item) use ($contents) {
                $item->setContent($contents[$item->name]);
                $item->save();
                
                if (class_basename($this->previous) == 'DynHead' ) $this->previous->setSlug($item->content);

                $this->previous->conNext($item);
                $this->previous->save();

                $this->previous = $item;
            }
        );
    }

    /** adds exta fields like component names and placeholder texts */
    public function dashboardFields()
    {

        $fields = $this->fields();

        $this->models->map(
            function ($model) use ($fields) {

                if (isset($fields[$model->name]['fields'])) {

                    foreach ($fields[$model->name]['fields'] as $key => $field) $model->$key = $field; 
                }
                
                if ($model->model === null) $model->model = $model->name;
                if ($model->title === null) $model->title = $model->name;
            }
        );

        return $this;
    }

    private function getNewModels() 
    {
        $this->dynHead = new DynHead;
        $this->dynHead->type = class_basename(Str::lower(class_basename($this)));

        $this->models = $this->generateComponents();
    }

    private function generateComponents() : Collection 
    {
        return $this->fields()->map(
                function ($item, $key) {

                    $model = $this->modelPath .  Str::ucfirst($item['type']);
                    $model = (new $model);
                    $model->name = $key;

                    return $model; 
                }
            );
    }
}
