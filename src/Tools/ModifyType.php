<?php 

namespace TheRiptide\LaravelDynamicDashboard\Tools;

use Illuminate\Support\Str;
use TheRiptide\LaravelDynamicDashboard\Traits\GetType;

class ModifyType {

    use GetType;

    private $previous;
    private $fields;
    private $models;

    public function run($type = null)
    {
        $types = $this->getType($type)
            ->get()
            ->map(
                function ($item) 
                {
                    if (! $item->modelsMatchFields()) $this->setup($item);
                }
            );
    }

    private function setup($item)
    {
        $previous = $item->head();

        $this->models = $item->models();
        $this->fields = $item->fields()->mapWithKeys(function ($field, $key) {
            return [
                $key => [
                    'type' => $field['type'], 
                    'name' => $key
                ] 
            ];
        });

        $this->compare($this->fields->shift(), $this->models->shift(), $previous);

        // foreach ($item->models() as $model)
        // {       
            // dump(['model' => $model, 'field' => $field]);
            // if ($field && $model->name == $field['name'] && 'Dyn' . Str::ucfirst($field['type']) == class_basename($model))
            // {
            //     $this->previous = $model;
            //     $field = $fields->shift();
            // }
            // else {
            //     if (! isset($fields[$model->name])) $previous = $this->removeModel($previous, $model);
            //     if (isset($fields[$model->name])) {
            //         $previous = $this->insertModel($previous, $model, $field);
            //     }
            // }    
        // }
    }

    private function compare($field, $model, $previous)
    {
        if ($field && $model->name == $field['name'] && 'Dyn' . Str::ucfirst($field['type']) == class_basename($model))
        {
            $previous = $model;
            $field = $this->fields->shift();
            $model = $this->models->shift();
        }
        else {
            if (! isset($this->fields[$model->name])) 
            {
                $previous = $this->removeModel($previous, $model);
                $model = $this->models->shift();
            }
            elseif (isset($this->fields[$model->name])) 
            {
                $previous = $this->insertModel($previous, $model, $field);
                $field = $this->fields->shift();
            }
        }

        if ($field != null && $model != null) $this->compare($field, $model, $previous);
    }

    private function removeModel($previous, $current)
    {
        dump('boop');
        $previous->next_model = $current->next_model;
        $previous->next_model_id = $current->next_model_id;

        $previous->save();
        $current->delete();

        return $previous;
    }

    private function insertModel($previous, $next, $field)
    {
        dump('beep');
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