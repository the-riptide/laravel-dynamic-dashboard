<?php 

namespace TheRiptide\LaravelDynamicDashboard\Tools;

use Illuminate\Support\Str;
use TheRiptide\LaravelDynamicDashboard\Traits\GetType;

class ModifyType {

    use GetType;

    private $models;
    private $type;

    public function run($type = null)
    {
        $this->getType($type)
            ->get()
            ->map( fn ($item) => $this->fix($item) );
    }

    private function fix($item)
    {
        $this->type = $item;
        
        [$previous, $models, $fields] = $this->setup($item);

        $this->compare($fields->shift(), $models->shift(), $previous, $fields, $models, collect());
    }

    private function setup($item)
    {
        $models = $item->models();
        $this->models = $item->head()->getAll();
        
        $fields = $item->fields()->mapWithKeys(function ($field, $key) {
            return [
                $key => [
                    'type' => $field['type'], 
                    'name' => $key
                ] 
            ];
        });
        $this->fields = $fields;

        return [$item->head(), $models, $fields];
    }

    private function compare($field, $model, $previous, $fields, $models, $moved)
    {
        dump(['previous' => $previous->name, 'field' => $field['name'], 'model' => $model->name]);

        dump('top');
        if ($field && $model->name == $field['name'] && 'Dyn' . Str::ucfirst($field['type']) == class_basename($model) && ! isset($moved[$field['name']]))
        {
            dump('okay');
            $previous = $model;
            $field = $fields->shift();
            $model = $models->shift();
        }
        else 
        {
            if (isset($moved[$field['name']]))
            {
                $previous = $this->switchModel($previous, $models->shift(),  $moved[$field['name']]);

                $model = $models->shift();
                $moved->forget($field['name']);
                $field = $fields->shift();
            }
            elseif ($field && isset($this->fields[$model->name]) && $this->models->where('name', $field['name'])->first() != null) 
            {
                $previous = $this->switchModel($previous, $model, $this->models->where('name', $field['name'])->first());
                
                $moved = $moved->merge([$model->name => $model]);
                $field = $fields->shift();
            }
            elseif (isset($fields[$model->name])) 
            {
                $previous = $this->insertModel($previous, $model, $field);
                $field = $fields->shift();
            }
            else  
            {
                $previous = $this->removeModel($previous, $model);
                $model = $models->shift();
            }
        }

        if ($field != null && $model != null) $this->compare($field, $model, $previous, $fields, $models, $moved);
    }

    private function switchModel($previous, $current, $replacement)
    {
        dump('switch');
        $this->threeConnext($previous, $replacement, $current->getNext() );

        return $replacement;
    }

    private function threeConnext($first, $second, $third)
    {
        dump('three');
        $first->connext($second);
        $first->save();
        $second->connext($third);
        $second->save();

    }

    private function removeModel($previous, $current)
    {
        dump('remove');
        $previous->next_model = $current->next_model;
        $previous->next_model_id = $current->next_model_id;

        $previous->save();
        $current->delete();

        return $previous;
    }

    private function insertModel($previous, $next, $field)
    {
        dump('insert');
        $current = 'TheRiptide\LaravelDynamicDashboard\Models\Dyn' . Str::ucfirst($field['type']);
        $current = new $current;

        $current->name = $field['name'];
        $current->connext($next);
        $current->save();

        $previous->connext($current);
        $previous->save();

        return $current;
    }
}