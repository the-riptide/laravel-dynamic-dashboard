<?php

namespace TheRiptide\LaravelDynamicDashboard\Models;

use TheRiptide\LaravelDynamicDashboard\Models\DynModel;

class DynText extends DynModel
{
    public $component = 'text';
    public $rules = 'required|string';

}
