<?php

namespace TheRiptide\LaravelDynamicDashboard\Models;

use TheRiptide\LaravelDynamicDashboard\Models\DynModel;

class DynDate extends DynModel
{
    public $rules = 'required|date';
    public $component = 'simple';
    public $type = 'date';

}
