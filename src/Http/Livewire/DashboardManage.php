<?php

namespace TheRiptide\LaravelDynamicDashboard\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use TheRiptide\LaravelDynamicDashboard\Objects\Menu;
use Illuminate\Support\Facades\Cache;
use TheRiptide\LaravelDynamicDashboard\Security\Authorize;
use TheRiptide\LaravelDynamicDashboard\Traits\GetType;

class DashboardManage extends Component
{
    use WithFileUploads, GetType;

    public $head;
    public $rules;
    public $type;
    public $identifier;
    private $previous;
    public $disabled;

    public function mount($type, $id = null)
    {
        $this->type = $type;
        $this->identifer = $id;                

        $dynamic = $this->getType($type, $id, false);

        $dynamic = $dynamic->getDashboardFields();

        $this->rules = $dynamic->rules();
        
        $dynamic->models()->map(
            fn ($item) => $this->{$item->name} = $item->getContent()
        );

        Cache::put('dynamicObject', $dynamic);
    }

    public function rules()
    {
        return $this->rules->toArray();
    }

    public function render()
    {

        $this->disabled = false;

        return view('dyndash::manage', [
            'fields' => Cache::get('dynamicObject')->models(),
        ])->extends('dashcomp::layout', ['menuItems' => (new Menu)->items ])
        ->section('body');
    }

    public function save()
    {
        $this->disabled = true;

        if (! (New Authorize)->canTakeAction()) return abort (403);

        $this->validate();

        $dynamic = Cache::get('dynamicObject');

        $content = $dynamic->models()
        ->mapWithKeys(
            fn ($item) => [$item->name => $this->{$item->name}] 
        );

        $dynamic->create($content);

        return redirect()->route('dyndash.index', [$this->type])->with("status", $this->type . " saved successfully!");
    }
}
