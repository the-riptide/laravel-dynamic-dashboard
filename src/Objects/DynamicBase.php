<?php 

namespace TheRiptide\LaravelDynamicDashboard\Objects;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use TheRiptide\LaravelDynamicDashboard\Models\DynHead;
use TheRiptide\LaravelDynamicDashboard\Traits\GetType;

class DynamicBase {

    use GetType;

    public $slug;
    public $user_id;
    public $id;
    
    private $dyn_models;
    private $dyn_head;
    private $dyn_type;

    protected $canDelete = true;
    protected $canCreate = true;
    protected $order_by = 'updated_at';

    private $modelPath = 'TheRiptide\LaravelDynamicDashboard\Models\Dyn';
    
    public function __construct(DynHead|string $head = null) {

        if (is_string($head)) $head = DynHead::firstWhere('slug', $head)->first();
        
        $head && $head->dyn_type == class_basename($this)
            ? $this->prepare($head)
            : $this->getNewModels();
    }

    public function canDelete()
    {
        return $this->canDelete;
    }

    public function canCreate()
    {
        return $this->canCreate;
    }

    public function setOrder()
    {
        return $this->order_by;
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
        return $this->dyn_models;
    }

    public function head()
    {
        return $this->dyn_head;
    }

    public function type()
    {
        return $this->dyn_type;
    }

    public function rules()
    {
        return $this->dyn_models
            ->filter(
                fn ($item) => $item->rules
            )->mapWithKeys(
                function ($item) {
                    
                    if ($item->rules) {
                            
                        if (is_array($item->rules)) {
                            return [$item->name => $item->exists ? $item->rules[1] : $item->rules[0] ];
                        }    
                        else { 
                            return [$item->name => $item->rules];
                        }                        
                    }
                }
            );
    }

    private function prepare($head)
    {
        if (is_string($head)) $head = DynHead::firstWhere('slug', $head);
        $this->dyn_models = $head->getAll();
        $this->dyn_head = $this->dyn_models->shift();
        $this->prepHead($this->dyn_head);
        $this->prepFields($this->dyn_models);
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

    public function create($contents) 
    {
        $this->previous = $this->dyn_head;
        
        $this->dyn_models->map(
            function ($item) use ($contents) {
                $item->unsetTempAttributes();
                $item->setContent($contents[$item->name], $this->dyn_head->dyn_type);
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

        $this->dyn_models->map(
            function ($model) use ($fields) {

                if (isset($fields[$model->name]['properties'])) {

                    foreach ($fields[$model->name]['properties'] as $key => $field) $model->$key = $field; 
                }
                
                if ($model->model === null) $model->model = $model->name;
                if ($model->title === null) $model->title = $model->name;
            }
        );

        return $this;
    }

    private function getNewModels() 
    {
        $this->dyn_head = new DynHead;
        $this->dyn_head->dyn_type = class_basename($this);

        $this->dyn_models = $this->generateComponents();
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
