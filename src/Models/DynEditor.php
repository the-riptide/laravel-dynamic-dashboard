<?php

namespace TheRiptide\LaravelDynamicDashboard\Models;

use TheRiptide\LaravelDynamicDashboard\Models\DynModel;

class DynEditor extends DynModel
{
    public $component = 'tinymce';
    public $rules = 'required|string';

    protected $table = 'dyn_texts';

}
