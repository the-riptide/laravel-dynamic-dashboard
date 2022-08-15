<?php 

namespace TheRiptide\LaravelDynamicDashboard\Tools;

use TheRiptide\LaravelDynamicDashboard\Traits\GetType;

class UpdateType {

    use GetType, UseCache;

    private $models;
    private $fields;
    private $type;

    public function run()
    {
        $this->getAllTypes()->map(function ($type) {

            $this->getType($type)
                ->get(false)
                ->map( 
                    fn ($item) => $this->fix($item)
                );                    
            }
        );
    }

    private function fix($item)
    {

        $head = $item->head();
        $item->models()
        ->map(
            function ($model) use ($head) 
            {
                $model->attachHead($head);
                $model->save();
            }
        );
    }
}