<?php

namespace TheRiptide\LaravelDynamicDashboard\Models;

use TheRiptide\LaravelDynamicDashboard\Models\DynModel;

class DynDate extends DynModel
{
    public $rules = 'required|date';
    public $component = 'simple';
    public $type = 'date';

    protected $casts = [
        'content' => 'datetime',
    ];


    public function getContent()
    {
        return $this->content
            ? $this->content->format('Y-m-d')
            : null;
    }


}
