<?php

namespace TheRiptide\LaravelDynamicDashboard\Models;

use TheRiptide\LaravelDynamicDashboard\Models\DynModel;

class DynTime extends DynModel
{
    public $rules = 'required';
    public $component = 'simple';
    public $type = 'time';

    protected $table = 'dyn_dates';

    protected $casts = [
        'content' => 'datetime',
    ];

    public function getContent()
    {
        return $this->content
            ? $this->content->format('H:i')
            : null;
    }
}
