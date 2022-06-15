<?php

namespace TheRiptide\LaravelDynamicDashboard\Models;

use TheRiptide\LaravelDynamicDashboard\Factories\DynBooleanFactory;
use TheRiptide\LaravelDynamicDashboard\Models\DynModel;

class DynBoolean extends DynModel
{
    public $rules = 'nullable';
    public $component = 'dashcomp::input.checkbox';

    protected static function newFactory()
    {
        return DynBooleanFactory::new();
    }

    public function setContent($content, $type) {

        $this->content = $content ? true : false;
    }
}
