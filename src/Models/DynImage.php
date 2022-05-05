<?php

namespace TheRiptide\LaravelDynamicDashboard\Models;

use TheRiptide\LaravelDynamicDashboard\Models\DynModel;

class DynImage extends DynModel
{

    protected $table = 'dyn_strings';
    public $component = 'image';
 
    public $rules = ['required|image', 'nullable|image'];    

    public function setContent($content) {

        $this->content = 'dynImages' . '/' . $content->store('/', 'dynImages');
    }
}
