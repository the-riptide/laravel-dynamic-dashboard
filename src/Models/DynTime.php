<?php

namespace TheRiptide\LaravelDynamicDashboard\Models;

use TheRiptide\LaravelDynamicDashboard\Models\DynModel;
use TheRiptide\LaravelDynamicDashboard\Factories\DynTimeFactory;

class DynTime extends DynModel
{
    public $rules = 'required';
    public $type = 'time';

    protected $table = 'dyn_dates';

    protected $casts = [
        'content' => 'datetime',
    ];

    protected static function newFactory()
    {
        return DynTimeFactory::new();
    }

    public function getContent()
    {
        return $this->content
            ? $this->content->format('H:i')
            : null;
    }
}
