<?php

namespace TheRiptide\LaravelDynamicDashboard\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use TheRiptide\LaravelDynamicDashboard\Objects\Manage;
use TheRiptide\LaravelDynamicDashboard\Objects\Menu;
use Illuminate\Support\Facades\Cache;
use TheRiptide\LaravelDynamicDashboard\Security\Authorize;

class DashboardManage extends Component
{

    use WithFileUploads;

    public $head;
    public $rules;
    public $type;
    public $identifier;
    private $previous;

    public function mount($type, $id = null)
    {
        $this->type = $type;
        $this->identifer = $id;                

        $dynamic = (new Manage($this->type, $this->identifer))->dashPrep();    
        $dynamic->models->shift();
        $this->rules = $dynamic->rules();

        $dynamic->models->map(fn ($item) => $this->{$item->name} = $item->content);
        Cache::put('dynamicModels', $dynamic->models);
    }

    public function rules()
    {
        return $this->rules->toArray();
    }

    public function render()
    {
        return view('dyndash::manage', [
            'fields' => Cache::get('dynamicModels'),
        ])->extends('dyndash::layout', ['menuItems' => (new Menu)->items ])
        ->section('body');
    }

    public function save()
    {
        if (! (New Authorize)->canTakeAction()) return abort (403);

        $this->validate();

        $dynamic = (new Manage($this->type, $this->identifer));
        $models = $dynamic->models;
        $this->previous = $models->shift();
        
        $models->map(
            function ($item) {
                $item->setContent($this->{$item->name});
                $item->save();
                
                if (class_basename($this->previous) == 'DynHead' ) $this->previous->setSlug($item->content);

                $this->previous->conNext($item);
                $this->previous->save();

                $this->previous = $item;
            }
        );

        return redirect()->route('dyndash.index', [$this->type]);
    }
}
