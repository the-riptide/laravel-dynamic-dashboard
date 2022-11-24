<?php 

namespace TheRiptide\LaravelDynamicDashboard\Models;

use TheRiptide\LaravelDynamicDashboard\Models\DynBase;

abstract class DynModel extends DynBase 
{
    
    public $rules = 'required';  
    public $component = 'dashcomp::input.simple';
    protected $guarded = [];

    public function setContent($content, $dynHead)
    {
        $this->content = $content;
        $this->attachHead($dynHead);
    }

    public function getContent()
    {
        return $this->content;
    }

    public function attachHead($head)
    {
        $this->dyn_type = $head->dyn_type;
        $this->dyn_head_id = $head->id;
    }
}


