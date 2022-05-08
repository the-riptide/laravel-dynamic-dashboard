<?php 

namespace TheRiptide\LaravelDynamicDashboard\Objects;

use TheRiptide\LaravelDynamicDashboard\Models\DynHead;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class Full {

    public $slug;
    public $user_id;
    public $type;
    public $id;
    
    public function __construct(DynHead $head) {

        $models = $head->getAll();
        $this->prepHead($models->shift());
        $this->prepFields($models);
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
