<?php

namespace TheRiptide\LaravelDynamicDashboard\Models;

use TheRiptide\LaravelDynamicDashboard\Models\DynModel;

class DynImage extends DynModel
{
    protected $table = 'dyn_strings';
    public $component = 'image';
 
    public $rules = ['required|image', 'nullable|image'];    

    public function setContent($content) {

        if (is_string($content)) $this->content = $content; 
        else {

            $this->content = 'dynImages' . '/' . $content->store('/dynImages', 'public');
        }    
    }
}
