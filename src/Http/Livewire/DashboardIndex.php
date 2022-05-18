<?php

namespace TheRiptide\LaravelDynamicDashboard\Http\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use TheRiptide\LaravelDynamicDashboard\Models\DynHead;
use Livewire\Component;
use TheRiptide\LaravelDynamicDashboard\Objects\Menu;
use TheRiptide\LaravelDynamicDashboard\Objects\Index;
use TheRiptide\LaravelDynamicDashboard\Collections\Dynamic;
use TheRiptide\LaravelDynamicDashboard\Security\Authorize;

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

        $this->posts = (New Dynamic($this->type))->links();
        $this->canDelete = $this->posts->first()->canDelete();   
        $this->heads = $this->posts->first()->tableHeads();

        return view('dyndash::index', [
            'field' => $this->heads,

        ])->extends('dyndash::layout', [
            'menuItems' => (new Menu)->items
        ])
            ->section('body');
    }

    public function delete()
    {
        if (! (new Authorize)->canTakeAction()) return abort(403);

        DynHead::find($this->deleteId)->deleteAll();

    }
}
