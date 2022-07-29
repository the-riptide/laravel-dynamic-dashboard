<?php

namespace TheRiptide\LaravelDynamicDashboard\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Cache;
use TheRiptide\LaravelDynamicDashboard\Objects\Menu;
use TheRiptide\LaravelDynamicDashboard\Traits\GetType;
use TheRiptide\LaravelDynamicDashboard\Security\Authorize;

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

        $relations = $dynamic->getRelationshipsForDashboard();
        $relations->map(fn ($item) => $this->{$item->model} = $item->selected);
        
        Session(['dynamicRelations' =>  $relations]);
        Session(['dynamicObject' => $dynamic]);
    }

    public function rules()
    {
        return $this->rules->toArray();
    }

    public function render()
    {
        $this->disabled = false;

        return view('dyndash::manage', [
            'fields' => Session('dynamicObject')->models(),
            'relations' => Session('dynamicRelations'),
        ])->extends('dashcomp::layout', ['menuItems' => (new Menu)->items ])
        ->section('body');
    }

    public function save()
    {
        $this->disabled = true;

        if (! (New Authorize)->canTakeAction()) return abort (403);

        $this->validate();

        $dynamic = $this->updateModels(Session('dynamicObject'));
        $this->updateRelations($dynamic);

        return redirect()->route('dyndash.index', [$this->type])->with("status", $this->type . " saved successfully!");
    }

    private function updateModels($dynamic)
    {
        return $dynamic->create(
            $dynamic->models()
                ->mapWithKeys(
                    fn ($item) => [$item->name => $this->{$item->name}] 
                )
        );
    }

    private function updateRelations($dynamic)
    {
        Session('dynamicRelations')->map(
            fn ($relation) => $dynamic->sync($relation->relationship, collect($this->{$relation->model}))
        );
    }
}
