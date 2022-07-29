<?php 

namespace TheRiptide\LaravelDynamicDashboard\Objects;

use Illuminate\Support\Str;
use TheRiptide\LaravelDynamicDashboard\Models\DynHead;
use TheRiptide\LaravelDynamicDashboard\Objects\DynamicBase;

class DashboardRelation
{
    public $items;
    public $selected;
    public $relationship;
    public $model;

    public $component = 'dashcomp::input.multi-dropdown';

    public function __construct(DynamicBase $base, $relationship, $content)
    {
        $this->relationship = $relationship;
        $this->model = 'rel_' .Str::of($relationship)->snake();
        $this->items = $this->prepItems($content); 
        $this->selected = $base->relation($relationship)->pluck('id');
        $this->component = isset($content['component']) ? $content['component'] : $this->component;
    }

    private function prepItems($content)
    {
        $field = isset($content['show']) ? $content['show'] : 'head';

        return DynHead::where('dyn_type', $this->relationship)->get()
        ->mapWithKeys( 
            function ($head) use($field) { 
                $item = $head->getType();
                return [
                    $item->id => $item->$field
                ];
            }
        )->sort();
    }
}
