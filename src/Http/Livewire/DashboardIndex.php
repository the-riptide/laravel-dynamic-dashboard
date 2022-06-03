<?php

namespace TheRiptide\LaravelDynamicDashboard\Http\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use TheRiptide\LaravelDynamicDashboard\Models\DynHead;
use Livewire\Component;
use TheRiptide\LaravelDynamicDashboard\Objects\Menu;
use TheRiptide\LaravelDynamicDashboard\Collections\DynamicCollection;
use TheRiptide\LaravelDynamicDashboard\Security\Authorize;
use TheRiptide\LaravelDynamicDashboard\Traits\GetType;

class DashboardIndex extends Component
{

    use GetType;

    public $deleteId = false;
    public $heads;
    public $posts;
    public $type;
    public $canDelete;
    public $canCreate;

    public function mount($type) 
    {
        $this->type = $type;

    }

    public function render()
    {
        
        $object = $this->getType($this->type);
        $this->posts = (New DynamicCollection($this->type))->get()->sortBy($object->setOrder());

        $this->canDelete = $object->canDelete();   
        $this->canCreate = $object->canCreate();   
        $this->heads = $object->tableHeads();

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
