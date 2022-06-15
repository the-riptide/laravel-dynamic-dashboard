<?php

namespace TheRiptide\LaravelDynamicDashboard\Models;

use TheRiptide\LaravelDynamicDashboard\Models\DynModel;
use TheRiptide\LaravelDynamicDashboard\Factories\DynEditorFactory;

class DynEditor extends DynModel
{
    public $component = 'dashcomp::input.tinymce';
    public $rules = 'required|string';

    protected $table = 'dyn_texts';


    protected static function newFactory()
    {
        return DynEditorFactory::new();
    }

}
