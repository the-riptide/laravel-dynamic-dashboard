<?php

namespace TheRiptide\LaravelDynamicDashboard\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

abstract class DynBase extends Model
{
    use HasFactory;

    private $path = 'TheRiptide\LaravelDynamicDashboard\Models\\'; 

    public function conNext($model) : Model {

        $this->next_model = $model ? class_basename($model) : null;
        $this->next_model_id = $model ? $model->id : null;

        return $this;
    }

    public function getNext() : Model|null {

        if ($this->next_model) {

            $model = $this->path . $this->next_model;
            return (new $model)->find($this->next_model_id);
        }

        else return null;
    }

    public function getType()
    {
        $model = $this->dyn_type == 'TestType'
            ? 'TheRiptide\LaravelDynamicDashboard\Types\TestType'
            : 'App\\' . config('dyndash.folder') . '\\' . Str::of($this->dyn_type)->camel()->ucfirst(); 

        return $this->exists() 
            ? (new $model($this))->find($this->id)
            : (new $model($this))->new();
    }
    
    public function getAll(Collection|null $collection = null) : Collection {

        if (!$collection) $collection = collect([$this]);

        if ($model = $this->getNext()) {
            return $model->getAll($collection->push($model));
        }
        else {
            return $collection;
        }
    }

    public function unsetTempAttributes() : Model
    {
        $fields = Schema::getColumnListing($this->getTable());

        foreach($this->getAttributes() as $key => $attribute)
        {
            if (!in_array($key, $fields)) {
                
                unset($this->$key);
            } 
        }
        return $this;
    }

    protected function deleteNext() {

        $next = $this->getNext();
        if ($next) $next->deleteNext();
        
        $this->delete();
    }
}