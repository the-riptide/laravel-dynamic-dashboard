<?php 

namespace TheRiptide\LaravelDynamicDashboard\Tools;

use Illuminate\Support\Str;
use TheRiptide\LaravelDynamicDashboard\Traits\GetType;

class ModifyType {

    use GetType, UseCache;

    private $models;
    private $fields;
    private $type;

    public function run($type = null)
    {
        $this->getType($type)
            ->get(false)
            ->map( function ($item) { 
                $this->fix($item); 
            });        
    }

    private function fix($item)
    {
        $this->type = $item;
        
        [$previous, $models, $fields] = $this->setup($item);

        $this->compare($fields->shift(), $models->shift(), $previous, $fields, $models, collect());

        $this->putCache($item->head()->dyn_type, $item->head()->id, $item->fresh());

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

        $this->fields = $fields->map(fn ($field) => $field['name'])->values();

        return [$item->head(), $models, $fields];
    }

    private function compare($field, $model, $previous, $fields, $models, $moved)
    {        

        if ($model != null && $field && $model->name == $field['name'] && 'Dyn' . Str::ucfirst($field['type']) == class_basename($model))
        {
            $previous = $model;
            $field = $fields->shift();
            $model = $models->shift();
        }
        else
        {            
            if ($model != null && $field && isset($fields[$model->name]) && $this->models->where('name', $field['name'])->first() != null) 
            {
                $previous = $this->switchModel(
                    $previous, 
                    $model, 
                    $this->models->where('name', $field['name'])->first(),
                );

                $models = $this->switchModelsCollection($models, $model, $previous);
                
                $field = $fields->shift();
                $model = $models->shift();
            }
            elseif ($model != null && $field['name'] == $model->name && $field['type'] != class_basename($model))
            {
                $previous = $this->changeModelType($field, $previous, $model);

                $field = $fields->shift();
                $model = $models->shift();
            }
            elseif ($model == null || isset($fields[$model->name])) 
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

        if ($field != null || $model != null) $this->compare($field, $model, $previous, $fields, $models, $moved);
    }

    private function changeModelType($field, $previous, $model)
    {
        $new = $this->createNewModel($field['type']);

        $new->name = $model->name;
        $new->content = $model->content;
        $new->next_model = $model->next_model;
        $new->next_model_id = $model->next_model_id;
        $new->save();

        $previous->connext($new);
        $previous->save();

        return $new;
    }

    private function switchModelsCollection($models, $replacement, $current)
    {

        $models->splice($models->search(fn ($item) => $item->name == $current->name), 1, [$replacement->fresh()]);

        return $models;
    }

    private function switchModel($previous, $current, $replacement)
    {
        $previous->connext($replacement);
        $previous->save();

        $replacement->connext($this->findModelInFieldsArray($replacement, 'next'));
        $replacement->save();

        $previous = $this->findModelInFieldsArray($current, 'previous');
        $previous->connext($current);
        $previous->save();

        $current->connext($this->findModelInFieldsArray($current, 'next'));
        $current->save();

        return $replacement;
    }

    private function removeModel($previous, $current)
    {
        $previous->next_model = $current->next_model;
        $previous->next_model_id = $current->next_model_id;

        $previous->save();
        $current->delete();

        return $previous;
    }

    private function createNewModel($type)
    {
        $current = 'TheRiptide\LaravelDynamicDashboard\Models\Dyn' . Str::ucfirst($type);
        return new $current;
    }

    private function insertModel($previous, $next, $field)
    {        
        $current = $this->createNewModel($field['type']);

        $current->name = $field['name'];
        $current->connext($next);
        $current->save();

        $previous->connext($current);
        $previous->save();

        return $current;
    }

    private function findModelInFieldsArray($model, $next)
    {
        $index = $this->fields->search(fn ($item) => $model->name == $item) + ($next == 'next' ? + 1 : - 1);

        return $index < $this->fields->count()
            ? $this->models->where('name', $this->fields[$index])->first()
            : null;
    }
}