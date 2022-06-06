<?php

namespace TheRiptide\LaravelDynamicDashboard\Http\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use TheRiptide\LaravelDynamicDashboard\Models\DynHead;
use Livewire\Component;
use TheRiptide\LaravelDynamicDashboard\Objects\Menu;
use TheRiptide\LaravelDynamicDashboard\Collections\DynamicCollection;
use TheRiptide\LaravelDynamicDashboard\Security\Authorize;
use TheRiptide\LaravelDynamicDashboard\Tools\SetOrder;
use TheRiptide\LaravelDynamicDashboard\Traits\GetType;

class DashboardIndex extends Component
{

    use GetType;

    public $deleteId = false;
    public $type;
    public $openOrder;

    public function mount($type) 
    {
        $this->type = $type;
        $this->openOrder = false;

    }


    public function setOrderEvent($begin, $end)
    {        

        (new SetOrder((New DynamicCollection($this->type))->get()->sortBy('dyn_order')))->set($begin, $end);


        $this->openOrder = false;

    }


    public function render()
    {       
        
        $object = $this->getType($this->type);

        return view('dyndash::index', [
            'heads' => $object->tableHeads(),
            'canOrder' =>  $object->canOrder(),
            'canDelete' => $object->canDelete(),
            'canCreate' => $object->canCreate(),
            'posts' => (New DynamicCollection($this->type))->get()->sortBy($object->setOrderBy()),


        ])->extends('dashcomp::layout', [
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
