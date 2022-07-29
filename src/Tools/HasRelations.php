<?php

namespace TheRiptide\LaravelDynamicDashboard\Tools;

use Illuminate\Support\Collection;

use TheRiptide\LaravelDynamicDashboard\Models\DynHead;
use TheRiptide\LaravelDynamicDashboard\Models\DynRelation;
use TheRiptide\LaravelDynamicDashboard\Objects\DashboardRelation;
use TheRiptide\LaravelDynamicDashboard\Objects\DynamicBase;

trait HasRelations 
{
    public function detach(Collection|array|int|string $removes)
    {
        $removes = $this->collectArray($removes);

        (is_int($removes) || is_string($removes))
            ? $this->detachOne($removes)
            : $removes->map(fn ($remove) => $this->detachOne($remove) );
    }

    public function attach(Collection|array|int|string $connects)
    {
        $connects = $this->collectArray($connects);

        (is_int($connects) || is_string($connects)) 
            ? $this->attachOne(DynHead::find($connects))
            : DynHead::wherein('id', $connects)->get()->map(fn ($connect) => $this->attachOne($connect));
    }

    public function sync($type, Collection|array $sync)
    {
        $sync = $this->collectArray($sync);

        $original = $this->relation($type)->pluck('id');

        $this->detach(DynHead::Wherein('id', $original->diff($sync))->pluck('id'));
        $this->attach( DynHead::Wherein('id', $sync->diff($original))->pluck('id'));
    }

    public function relation($type)
    {
        $ids = DynRelation::query()
            ->where(
                function($query) use ($type) {
                    $query->where('link_id', $this->id)
                        ->where('origin_type', $type);
                }
            )->orwhere(
                function($query) use ($type){
                    $query->where('origin_id', $this->id)
                        ->where('link_type', $type);
                }
            )->get()
            ->map(function ($item){
    
                return $item->origin_id == $this->id
                    ? $item->link_id
                    : $item->origin_id;  
            });
            
        return DynHead::wherein('id', $ids)->get()
        ->map(
            fn ($head) => $head->getType()
        );
    }

    public function getRelationshipsForDashboard()
    {
        return $this->relationships()
        ->map(
            fn ($content, $relationship) => New DashboardRelation($this, $relationship, $content)
        );
    }

    private function detachOne(string|int $remove)
    {
        DynRelation::where(function($query) use ($remove) {
            $query->where('origin_id', $this->id)
                ->where('link_id', $remove);
        })->orwhere(function ($query) use ($remove){
            $query->where('link_id', $this->id)
            ->where('origin_id', $remove);
        })->delete();
    }

    private function attachOne(DynHead $connect)
    {
        DynRelation::create([
            'origin_id' => $this->id,
            'link_id' =>  $connect->id,
            'origin_type' => $this->type(),
            'link_type' => $connect->dyn_type,       
        ]);
    }

    private function collectArray($array)
    {
        return is_array($array) 
            ? collect($array)
            : $array;
    }
}