<?php 

namespace TheRiptide\LaravelDynamicDashboard\Tools;

use Illuminate\Support\Str;
use TheRiptide\LaravelDynamicDashboard\Traits\GetType;

class ModifyType {

    use GetType;

    private $models;
    private $fields;
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

        $this->fields = $fields->map(fn ($field) => $field['name'])->values();

        return [$item->head(), $models, $fields];
    }

    private function compare($field, $model, $previous, $fields, $models, $moved)
    {        
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
            if ($field && isset($fields[$model->name]) && $this->models->where('name', $field['name'])->first() != null) 
            {
                $previous = $this->switchModel(
                    $previous, $model, $this->models->where('name', $field['name'])->first(),
                );

                $models = $this->switchModelsCollection($models, $model, $previous);
                
                $field = $fields->shift();
                $model = $models->shift();
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

    private function switchModelsCollection($models, $replacement, $current)
    {
        dump('switch model collection');

        $models->splice($models->search(fn ($item) => $item->name == $current->name), 1, [$replacement->fresh()]);

        // dump($models->map(fn ($model) => $model->name));
        return $models;
    }

    private function findModelInFieldsArray($model, $next)
    {
        $index = $this->fields->search(fn ($item) => $model->name == $item) + ($next == 'next' ? + 1 : - 1);

        return $index < $this->fields->count()
            ? $this->models->where('name', $this->fields[$index])->first()
            : null;
    }

    private function switchModel($previous, $current, $replacement)
    {
        dump('switch');

        $previous->connext($replacement);
        $previous->save();

        $replacement->connext($this->findModelInFieldsArray($replacement, 'next'));
        $replacement->save();

        $previous = $this->findModelInFieldsArray($current, 'previous');
        $previous->connext($current);
        $previous->save();

        $current->connext($this->findModelInFieldsArray($current, 'next'));
        $current->save();





        // $nextReplacement = $replacement->getNext();
        // $nextCurrent = $current->getNext();

        // $previousReplacement = $this->models->where('next_model_id', $replacement->id)->where('next_model', class_basename($replacement))->first(); 
        
        // dump([
        //     'previousCurrent' => $previousCurrent->name, 
        //     'current' => $current->name, 
        //     'nextCurrent' => $nextCurrent->name,
        //     'previousReplacement' => $previousReplacement->name, 
        //     'replacement' => $replacement->name, 
        //     'nextReplacement' => $nextReplacement->name ?? null
        // ]);

        // $previousCurrent->connext($replacement);
        // $previousCurrent->save();

        // $replacement->connext($nextCurrent == $replacement ? $previousReplacement : $nextCurrent);
        // $replacement->save();

        // $previousReplacement->connext($previousReplacement == $current ? $nextReplacement : $current);
        // $previousReplacement->save();

        // $current->connext($nextReplacement);
        // $current->save();

        return $replacement;
    }

    private function threeConnext($first, $second, $third)
    {
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