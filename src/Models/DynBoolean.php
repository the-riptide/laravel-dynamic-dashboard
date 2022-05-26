<?php

namespace TheRiptide\LaravelDynamicDashboard\Models;

use TheRiptide\LaravelDynamicDashboard\Models\DynModel;

class DynBoolean extends DynModel
{
    public $rules = 'nullable';
    public $component = 'checkbox';

    public function setContent($content, $type) {

        $this->content = $content ? true : false;
    }
}
