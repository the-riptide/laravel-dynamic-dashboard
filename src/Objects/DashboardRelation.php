<?php

namespace TheRiptide\LaravelDynamicDashboard\Objects;

use Illuminate\Support\Str;
use TheRiptide\LaravelDynamicDashboard\Models\DynHead;

class DashboardRelation
{
    public $items;

    public $selected;

    public $relationship;

    /** The key under the manage form's relations array, e.g. rel_experience_technology. */
    public $key;

    /** The wire:model path the input binds to. */
    public $model;

    public $component = 'dashcomp::input.multi-dropdown';

    public function __construct(DynamicBase $base, $relationship, $content)
    {
        $this->relationship = $relationship;
        $this->key = 'rel_'.Str::of($relationship)->snake();
        $this->model = 'relations.'.$this->key;
        $this->items = $this->prepItems($content);
        $this->selected = $base->relation($relationship)->pluck('id');
        $this->component = isset($content['component']) ? $content['component'] : $this->component;
    }

    private function prepItems($content)
    {
        $field = isset($content['show']) ? $content['show'] : 'head';

        return DynHead::where('dyn_type', $this->relationship)->get()
            ->mapWithKeys(
                function ($head) use ($field) {
                    $item = $head->getType();

                    return [
                        $item->id => $item->$field,
                    ];
                }
            )->sort();
    }
}
