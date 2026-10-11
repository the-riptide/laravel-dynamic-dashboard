<?php

namespace TheRiptide\LaravelDynamicDashboard\Http\Livewire;

use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use TheRiptide\LaravelDynamicDashboard\Objects\Menu;
use TheRiptide\LaravelDynamicDashboard\Security\Authorize;
use TheRiptide\LaravelDynamicDashboard\Traits\GetType;

class DashboardManage extends Component
{
    use GetType, WithFileUploads;

    public $head;

    public $rules;

    public $type;

    public $identifier;

    public $disabled;

    /** The form's field values, keyed by field name. Livewire 3 only round-trips declared properties. */
    public array $values = [];

    /** The selected ids per relationship, keyed rel_{relationship}. */
    public array $relations = [];

    public function mount($type, $id = null)
    {
        $this->type = $type;
        $this->identifier = $id;

        $dynamic = $this->getTypeOrAbort($type, $id)->getDashboardFields();
        $relations = $dynamic->getRelationshipsForDashboard();

        $this->rules = $dynamic->rules()->mapWithKeys(
            fn ($rule, $name) => [($relations->contains('key', $name) ? 'relations.' : 'values.').$name => $rule]
        )->all();

        $this->values = $dynamic->models()->mapWithKeys(fn ($item) => [$item->name => $item->getContent()])->all();
        $this->relations = $relations->mapWithKeys(fn ($item) => [$item->key => $item->selected->all()])->all();

        Session(['dynamicRelations' => $relations]);
        Session(['dynamicObject' => $dynamic]);
    }

    public function rules()
    {
        return $this->rules;
    }

    /** Messages name the field as before the values array: "The primary image field is required." */
    public function validationAttributes()
    {
        return collect($this->rules)->keys()->mapWithKeys(
            fn ($key) => [$key => str_replace('_', ' ', Str::snake(Str::after($key, '.')))]
        )->all();
    }

    public function render()
    {
        $this->disabled = false;

        return view('dyndash::manage', [
            'fields' => Session('dynamicObject')->models(),
            'relationInputs' => Session('dynamicRelations'),
        ])->extends('dashcomp::layout', ['menuItems' => (new Menu)->items])
            ->section('body');
    }

    public function save()
    {
        $this->disabled = true;

        if (! (new Authorize)->canTakeAction()) {
            return abort(403);
        }

        $this->validate();

        $dynamic = $this->updateModels(Session('dynamicObject'));
        $this->updateRelations($dynamic);

        return redirect()->route('dyndash.index', [$this->type])->with('status', $this->type.' saved successfully!');
    }

    public function removeImage($name)
    {
        if (! (new Authorize)->canTakeAction()) {
            return abort(403);
        }

        $dynamic = Session('dynamicObject')->fresh();
        $model = $dynamic->models()->where('name', $name)->first();
        $model->livewireRemoveImage($this->type);
        $model->update(['content' => null]);
        Session(['dynamicObject' => $dynamic->fresh()->getDashboardFields()]);

        // A later save keeps the field empty instead of writing the deleted path back.
        $this->values[$name] = null;
    }

    private function updateModels($dynamic)
    {
        return $dynamic->create(
            $dynamic->models()
                ->mapWithKeys(
                    fn ($item) => [$item->name => $this->values[$item->name] ?? null]
                )
        );
    }

    private function updateRelations($dynamic)
    {
        Session('dynamicRelations')->map(
            fn ($relation) => $dynamic->sync($relation->relationship, collect($this->relations[$relation->key] ?? []))
        );
    }
}
