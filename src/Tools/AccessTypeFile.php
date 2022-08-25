<?php 

namespace TheRiptide\LaravelDynamicDashboard\Tools;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use TheRiptide\LaravelDynamicDashboard\Objects\DynamicBase;

trait AccessTypeFile {

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
        return isset($this->index()[$field]['function']) && $this->index()[$field]['function'] 
            ? $this->$field()
            : $this->$field;
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

    /** an empty collection to avoid a relationships call in the dashboard generating an error */
    public function relationships() : Collection
    {
        return collect([]);
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

    protected function prepHead($head) 
    {
        Collect(Schema::getColumnListing($head->getTable()))->map(
            function ($field) use ($head) {
                if (! Str::startswith($field, 'next')) {
                    
                    $this->$field = $head->$field;
                }        
            }
        );
    }

    protected function prepFields($objects) 
    {
        $objects->map(fn ($item) => $this->{$item->name} = $item->content);
    }
    
    protected function getTableHead($key) : string
    {
        return isset($this->fields()[$key]['properties']['title'])
            ? $this->fields()[$key]['properties']['title']
            : $key;
    }
}