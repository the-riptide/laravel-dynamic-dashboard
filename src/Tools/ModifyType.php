<?php 

namespace TheRiptide\LaravelDynamicDashboard\Tools;

use Illuminate\Support\Str;
use TheRiptide\LaravelDynamicDashboard\Traits\GetType;

class ModifyType {

    use GetType;

    private $previous;
    private $current;

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
            if ($field && $model->name == $field['name'] && 'Dyn' . Str::ucfirst($field['type']) == $model->dyn_type)
            {
                $previous = $model;
                $field = $fields->shift();
            }

            else {
                if (! isset($fields[$model->name])) $this->removeAndReconnect($previous, $model);
            }    
        }
    }

    private function removeAndReconnect($last, $current)
    {
        $last->next_model = $current->next_model;
        $last->next_model_id = $current->next_model_id;

        $last->save();
        $current->delete();
    }
}