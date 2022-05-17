<?php 

namespace TheRiptide\LaravelDynamicDashboard\Objects;

use Illuminate\Database\Eloquent\Model;

class Type extends Model {

    public function prepForFront(Full $full)
    {
        foreach ($full as $key => $attribute) $this->$key = $attribute;

        return $this;
    }

    public function index() 
    {
        return $this->fields()->map(fn ($attribute, $key) => $key);
    }

    public function fields() 
    {
        return collect([]);
    }
}
