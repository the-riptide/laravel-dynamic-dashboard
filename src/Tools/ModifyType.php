<?php 

namespace TheRiptide\LaravelDynamicDashboard\Tools;

use Illuminate\Support\Str;
use TheRiptide\LaravelDynamicDashboard\Traits\GetType;

class ModifyType {

    use GetType;

    private $previous;

    public function run($type = null)
    {
        $types = $this->getType($type)
            ->get()
            ->map(
                function ($item) 
                {
                    if (! $item->modelsMatchFields()) $this->compareAndModify($item);
                }
            );
    }

    private function compareAndModify($item)
    {
        $previous = $item->head();
        $fields = $item->fields()->mapWithKeys(function ($field, $key) {
            return [
                $key => [
                    'type' => $field['type'], 
                    'name' => $key
                ] 
            ];
        });

        $field = $fields->shift();

        foreach ($item->models() as $model)
        {       
            dump(['model' => $model, 'field' => $field]);
            if ($field && $model->name == $field['name'] && 'Dyn' . Str::ucfirst($field['type']) == class_basename($model))
            {
                $this->previous = $model;
                $field = $fields->shift();
            }
            else {
                if (! isset($fields[$model->name])) $previous = $this->removeModel($previous, $model);
                if (isset($fields[$model->name])) {
                    $previous = $this->insertModel($previous, $model, $field);
                }
            }    
        }
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