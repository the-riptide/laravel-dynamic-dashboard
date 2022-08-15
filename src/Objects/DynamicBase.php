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

    /** Similar to 'find' on model. 
     * You can find using the DynHead model, the slug or the id.
     * The second parameter determines if the find should come from the cache.
    */
    public function find(DynHead|string $head, $getCache = true) : DynamicBase
    {
        if (is_string($head)) $head = is_numeric($head) ? DynHead::find($head) : DynHead::firstWhere('slug', $head);

        if ($getCache && $this->existCache($head->dyn_type, $head->id)) return $this->firstCache($head->dyn_type, $head->id);

        $this->dyn_models = $head->getAll();
        $this->dyn_head = $this->dyn_models->shift();
        $this->prepHead($this->dyn_head);
        $this->prepFields($this->dyn_models);

        $this->putInCache();

        return $this;
    }

    /** just like a standard laravel model, this returns the first instance. */
    public function first($getCache = true)
    {
        return ($head = DynHead::orderby($this->setOrderBy())->where('dyn_type', $this->new()->head()->dyn_type)->first()) 
            ? $this->find($head, $getCache) 
            : null;
    }

    /** updates the cache with the current instance */
    public function putInCache() {

        $this->putCache($this->dyn_head->dyn_type, $this->dyn_head->id, $this);
    }

    /** create a new instance of this type */
    public function new() : DynamicBase
    {
        $this->getNewModels();
        $this->prepFields($this->dyn_models);

        return $this;
    }

    /** fresh up the type from the database */
    public function fresh() : DynamicBase
    {
        return $this->find($this->id, false);
    }

    /** get all the Type. The parameter determines if the types come from the cache or the database  */
    public function get($getCache = true) : Collection
    {
        return (new DynamicCollection(class_basename($this), $getCache))->get();
    }

    /** a getter that returns if the type's delete has set 'canDelete' to true or not */
    public function canDelete() : bool
    {
        return $this->canDelete;
    }

    /** a getter that returns if the type's delete has set 'canCreate' to true or not */
    public function canCreate() : bool
    {
        return $this->canCreate;
    }

    /** a getter that returns if the type's delete has set 'canOrder' to true or not */
    public function canOrder() : bool
    {
        return $this->canOrder;
    }

    /** returns what the type should be ordered by */
    public function setOrderBy() : string
    {
        return $this->canOrder
            ? 'dyn_order'
            : $this->order_by;
    }
    
    /** returns the index table column heads */
    public function tableHeads() : Collection
    {
        return $this->index()->mapWithKeys(
            function($item, $itemKey) {

                if (is_array($item) || $item instanceof Collection) return [$this->getTableHead($itemKey) => $itemKey];

                return [$this->getTableHead($item) => $item];
            }
        )->filter();
    }

    /** works with the index function to determine if a dashboard index column is simply a value or a function */
    public function setValue($field) : string
    {
        if (isset($this->index()[$field]['function']) && $this->index()[$field]['function']) 
        {
            return $this->$field();
        } 

        return $this->$field;
    }

    /** this getter returns all the underlying models related to the field of this instance's type */
    public function models() : Collection
    {
        return $this->dyn_models;
    }

    /** this getter returns the linked list head related to this instance of  the type */
    public function head() : DynHead
    {
        return $this->dyn_head;
    }

    /** returns the type */
    public function type() : string
    {
        return $this->dyn_type;
    }

    /** returns the validation rules of the fields */
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
            )->merge(
                $this->relationships()
                ->mapWithKeys(
                    fn ($item, $key) => ['rel_' .Str::of($key)->snake() => 'nullable|array']
                )
            );
    }

    /** if the specific type has no index function, this will return all the existing fields */
    public function index() : Collection
    {
        return $this->fields()->map(fn ($attribute, $key) => $key);
    }

    /** if the specific type has no fields function, this will return an empty collection to avoid errors */
    public function fields() : Collection
    {
        return collect([]);
    }

    /** The create method will take an array of data, use it to create an instance of the current Type and return it */
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

    public function save() : DynamicBase
    {
        return $this->create();
    }

    /** this will run through all the underlying models and generate data for them using their factories */
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

    /** an empty collection to avoid a relationships call in the dashboard generating an error */
    public function relationships() : Collection
    {
        return collect([]);
    }

    private function prepHead($head) 
    {
        Collect(Schema::getColumnListing($head->getTable()))->map(
            function ($field) use ($head) {
                if (! Str::startswith($field, 'next')) {
                    
                    $this->$field = $head->$field;
                }        
            }
        );
    }

    private function prepFields($objects) 
    {
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

    private function getTableHead($key) : string
    {
        return isset($this->fields()[$key]['properties']['title'])
            ? $this->fields()[$key]['properties']['title']
            : $key;
    }
}
