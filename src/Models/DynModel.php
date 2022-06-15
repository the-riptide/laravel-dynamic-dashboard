<?php 

namespace TheRiptide\LaravelDynamicDashboard\Models;

use TheRiptide\LaravelDynamicDashboard\Models\DynBase;

abstract class DynModel extends DynBase 
{
    
    public $rules = 'required';  
    public $component = 'dashcomp::input.simple';

    public function setContent($content, $type)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

}


