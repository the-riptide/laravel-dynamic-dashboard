<?php 

namespace TheRiptide\LaravelDynamicDashboard\Objects;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use TheRiptide\LaravelDynamicDashboard\Models\DynHead;
use TheRiptide\LaravelDynamicDashboard\Tools\UseCache;
use TheRiptide\LaravelDynamicDashboard\Traits\GetType;
use TheRiptide\LaravelDynamicDashboard\Tools\HasRelations;
use TheRiptide\LaravelDynamicDashboard\Collections\DynamicCollection;

abstract class DynamicBase {

    use GetType, HasRelations, UseCache;

    public $slug;
    public $user_id;
    public $id;
    
    private $dyn_models;
    private $dyn_head;
    private $dyn_type;

    protected $canDelete = true;
    protected $canCreate = true;
    protected $canOrder = true;
    protected $order_by = 'updated_at';

    private $modelPath = 'TheRiptide\LaravelDynamicDashboard\Models\\';
    
    public function find(DynHead|string $head, $getCache = true) : DynamicBase
    {
        if (is_string($head)) 
        {
            $head = is_numeric($head) 
                ? DynHead::find($head)
                : DynHead::firstWhere('slug', $head);
        }


        if ($getCache && $this->existCache($head->dyn_type, $head->id)) return $this->firstCache($head->dyn_type, $head->id);

        $this->dyn_models = $head->getAll();
        $this->dyn_head = $this->dyn_models->shift();
        $this->prepHead($this->dyn_head);
        $this->prepFields($this->dyn_models);

        $this->putInCache();

        return $this;
    }

    public function putInCache() {

        $this->putCache($this->dyn_head->dyn_type, $this->dyn_head->id, $this);
    }

    public function new() : DynamicBase
    {
        $this->getNewModels();
        $this->prepFields($this->dyn_models);

        return $this;
    }

    public function fresh() : DynamicBase
    {
        return $this->find($this->id, false);
    }

    public function get($getCache = true) : Collection
    {
        return (new DynamicCollection(class_basename($this), $getCache))->get();
    }

    public function canDelete() : bool
    {
        return $this->canDelete;
    }

    public function canCreate() : bool
    {
        return $this->canCreate;
    }

    public function canOrder() : bool
    {
        return $this->canOrder;
    }

    public function setOrderBy() : string
    {
        return $this->canOrder
            ? 'dyn_order'
            : $this->order_by;
    }
    
    public function tableHeads() : Collection
    {
        return $this->index()->mapWithKeys(
            function($item, $itemKey) {

                if (is_array($item) || $item instanceof Collection) return [$this->getTableHead($itemKey) => $itemKey];

                return [$this->getTableHead($item) => $item];
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

    public function models() : Collection
    {
        return $this->dyn_models;
    }

    public function head() : DynHead
    {
        return $this->dyn_head;
    }

    public function type() : string
    {
        return $this->dyn_type;
    }

    public function rules() : Collection
    {
        return $this->dyn_models
            ->filter(
                fn ($item) => $item->rules
            )->mapWithKeys(
                function ($item) {
                    
                    if ($item->rules) 
                    {                            
                        if (is_array($item->rules)) return [$item->name => $item->exists ? $item->rules[1] : $item->rules[0] ];
                        
                        return [$item->name => $item->rules];
                    
                    }
                }
            );
    }

    public function index() : Collection
    {
        return $this->fields()->map(fn ($attribute, $key) => $key);
    }

    public function fields() : Collection
    {
        return collect([]);
    }

    public function create($contents = []) : DynamicBase 
    {
        if (! $this->dyn_head || ! $this->dyn_head->exists()) $this->getNewModels();

        $this->previous = $this->dyn_head;
        
        $this->toDataBase($contents);

        $this->prepHead($this->dyn_head);
        $this->prepFields($this->dyn_models);

        $this->putInCache();

        return $this;
    }

    public function factory() : DynamicBase
    {
        $model = $this->new();

        $this->contents = $model->dyn_models->mapWithKeys(
            function ($item) {
                $model = $this->modelPath .  class_basename($item);
                $model = (new $model)->factory()->create();
                return [$item->name => $model->content];
            }
        );

        return $this;
    }

    /** adds exta fields like component names and placeholder texts */
    public function getDashboardFields() : DynamicBase
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


    public function relationships() : Collection
    {
        return collect([]);
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

    private function toDataBase($contents)
    {
        if (isset($this->contents)) $contents = $this->contents->merge($contents);

        $this->dyn_models->map(
            function ($item) use ($contents) {

                $item->unsetTempAttributes();
                $item->setContent($contents[$item->name] ?? null, $this->dyn_head->dyn_type);
                $item->save();
                
                if (class_basename($this->previous) == 'DynHead' ) $this->previous->setSlug($item->content);

                $this->previous->conNext($item);
                $this->previous->save();

                $this->previous = $item;
            }
        );
    }

    private function getNewModels() : DynamicBase
    {
        $this->dyn_head = new DynHead;
        $this->dyn_head->dyn_type = class_basename($this);

        $this->dyn_models = $this->generateComponents();

        return $this;
    }

    private function generateComponents() : Collection 
    {
        return $this->fields()->map(
            function ($item, $key) {

                $model = $this->modelPath . 'Dyn' .  Str::ucfirst($item['type']);
                $model = (new $model);
                $model->name = $key;

                return $model; 
            }
        );
    }

    private function getTableHead($key)
    {
        return isset($this->fields()[$key]['properties']['title'])
            ? $this->fields()[$key]['properties']['title']
            : $key;
    }
}
