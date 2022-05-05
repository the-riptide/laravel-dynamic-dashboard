<?php

namespace TheRiptide\LaravelDynamicDashboard\Http\Livewire;

use TheRiptide\LaravelDynamicDashboard\Models\DynHead;
use Livewire\Component;
use TheRiptide\LaravelDynamicDashboard\Objects\Menu;
use TheRiptide\LaravelDynamicDashboard\Objects\Index;
use TheRiptide\LaravelDynamicDashboard\Collections\Dynamic;

class DashboardIndex extends Component
{
    public $deleteId = false;
    public $heads;
    public $posts;
    public $type;
    public $canDelete;

    public function mount($type) 
    {
        $this->type = $type;
    }

    public function render()
    {
        $this->posts = (New Dynamic($this->type))->links;
        $index = (new Index($this->type)); 
        $this->canDelete = $index->canDelete;   
        $this->heads = $index->heads;

        return view('dyndash::index', [
            'field' => $index,

        ])->extends('dyndash::layout', [
            'menuItems' => (new Menu)->items
        ])
            ->section('body');
    }

    public function delete()
    {
        DynHead::find($this->deleteId)->deleteAll();

    }
}
