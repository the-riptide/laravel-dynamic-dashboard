<?php 

namespace TheRiptide\LaravelDynamicDashboard\Objects;

use TheRiptide\LaravelDynamicDashboard\Models\DynHead;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class Manage extends Base
{
    public $models;
    private $type;
    private $modelPath = 'TheRiptide\LaravelDynamicDashboard\Models\Dyn';
    private $previous;

    public function __construct(string $type, int|null $id = null)
    {        
        $this->type = $this->getType($type);

        $this->models = $id ? $this->getSetModels($id) : $this->getNewModels();
    }

    /** adds exta fields like component names and placeholder texts */
    public function dashPrep()
    {
        $fields = $this->type->fields();

        $this->models->map(
            function ($model) use ($fields) {

                if (isset($fields[$model->name]['fields'])) {

                    foreach ($fields[$model->name]['fields'] as $key => $field) $model->$key = $field; 
                }
                
                if ($model->model === null) $model->model = $model->name;
                if ($model->title === null) $model->title = $model->name;
            }
        );


        
        return $this;
    }

    public function save($contents) 
    {

        $models = $this->models;

        $this->previous = $models->shift();
        
        $models->map(
            function ($item) use ($contents) {
                $item->setContent($contents[$item->name]);
                $item->save();
                
                if (class_basename($this->previous) == 'DynHead' ) $this->previous->setSlug($item->content);

                $this->previous->conNext($item);
                $this->previous->save();

                $this->previous = $item;
            }
        );
    }

    private function getSetModels(int $id) : Collection 
    {
        return DynHead::where('type', Str::lower(class_basename($this->type)))->find($id)->getAll();
    }   

    private function getNewModels() : Collection 
    {
        $head = new DynHead;
        $head->type = class_basename($this->type);

        return collect([$head])
            ->concat($this->generateComponents())
            ->flatten();
    }

    public function rules() : Collection
    {
        return $this->models
            ->filter(
                function ($item) {

                    return $item->rules;
                })->mapWithKeys(function ($item) {
                    if ($item->rules) {
                        
                        if (is_array($item->rules)) $rules = $item->exists() ? $item->rules[0] : $item->rules[1];    
                        else $rules = $item->rules;
                     
                        return [$item->name => $rules];
                    }
                }
            );
    }

    private function generateComponents() : Collection 
    {
        return collect([
            $this->type->fields()->map(
                function ($item, $key) {

                    $model = $this->modelPath .  Str::ucfirst($item['type']);
                    $model = (new $model);
                    $model->name = $key;

                    return $model; 
                }
            )
        ]);
    }

} 