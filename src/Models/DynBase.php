<?php

namespace TheRiptide\LaravelDynamicDashboard\Models;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

abstract class DynBase extends Model
{
    use HasFactory;

    private $path = 'TheRiptide\LaravelDynamicDashboard\Models\\'; 

    public function conNext($model) : Model {

        $this->next_model = class_basename($model);
        $this->next_model_id = $model->id;

        return $this;
    }

    public function getNext() : Model|null {

        if ($this->next_model) {

            $model = $this->path . $this->next_model;
            return (new $model)->find($this->next_model_id);
        }

        else return null;
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