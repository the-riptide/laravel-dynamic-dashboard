<?php

namespace TheRiptide\LaravelDynamicDashboard\Models;

class DynDropdown extends DynModel
{
    protected $table = 'dyn_strings';

    public $component = 'dashcomp::input.dropdown';

    protected static function newFactory()
    {
        return DynDropdown::new();
    }
}
