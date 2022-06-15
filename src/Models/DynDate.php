<?php

namespace TheRiptide\LaravelDynamicDashboard\Models;

use TheRiptide\LaravelDynamicDashboard\Factories\DynDateFactory;
use TheRiptide\LaravelDynamicDashboard\Models\DynModel;

class DynDate extends DynModel
{
    public $rules = 'required|date';
    public $type = 'date';

    protected $casts = [
        'content' => 'datetime',
    ];

    protected static function newFactory()
    {
        return DynDateFactory::new();
    }

    public function getContent()
    {
        return $this->content
            ? $this->content->format('Y-m-d')
            : null;
    }


}
