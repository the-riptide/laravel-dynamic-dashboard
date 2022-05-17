<?php 

namespace TheRiptide\LaravelDynamicDashboard\Objects;

use TheRiptide\LaravelDynamicDashboard\Models\DynHead;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class Item extends Base {

    public $slug;
    public $user_id;
    public $id;
    
    private $modelType;

    public function __construct(DynHead $head) {

        $models = $head->getAll();
        $this->prepHead($models->shift());
        $this->prepFields($models);

        $this->modelType = $this->getType($head->type);
    }

    public function toType() 
    {
        return $this->modelType->prepForFront($this);
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
}
