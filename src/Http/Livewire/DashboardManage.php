<?php

namespace TheRiptide\LaravelDynamicDashboard\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use TheRiptide\LaravelDynamicDashboard\Objects\Manage;
use TheRiptide\LaravelDynamicDashboard\Objects\Menu;
use Illuminate\Support\Facades\Cache;
use TheRiptide\LaravelDynamicDashboard\Security\Authorize;
use TheRiptide\LaravelDynamicDashboard\Traits\Types;

class DashboardManage extends Component
{
    use WithFileUploads, Types;

    public $head;
    public $rules;
    public $type;
    public $identifier;
    private $previous;

    public function mount($type, $id = null)
    {
        $this->type = $type;
        $this->identifer = $id;                

        $dynamic = $this->getType($type, $id);
        $dynamic = $dynamic->dashboardFields();

        $this->rules = $dynamic->rules();

        $dynamic->models()->map(fn ($item) => $this->{$item->name} = $item->content);

        Cache::put('dynamicModels', $dynamic->models());
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

        $dynamic = $this->getType($this->type, $this->identfier);

        $dynamic->save(
            $dynamic->models()
            ->mapWithKeys(
                fn ($item) => [$item->name => $this->{$item->name}] 
            )
        );

        return redirect()->route('dyndash.index', [$this->type]);
    }
}
