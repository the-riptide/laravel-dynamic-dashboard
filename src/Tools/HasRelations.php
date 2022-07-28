<?php

namespace TheRiptide\LaravelDynamicDashboard\Tools;

use Illuminate\Support\Collection;

use function PHPUnit\Framework\isInstanceOf;
use TheRiptide\LaravelDynamicDashboard\Models\DynHead;
use TheRiptide\LaravelDynamicDashboard\Models\DynRelation;
use TheRiptide\LaravelDynamicDashboard\Objects\DynamicBase;

trait HasRelations 
{
    protected $relationComponent = 'multidropdown';

    public function detach(DynamicBase|Collection $remove)
    {
        $remove instanceof Collection
            ? $remove->map(fn ($item) => $this->detachOne($item) )
            : $this->detachOne($remove);
    }

    private function detachOne(DynamicBase $remove)
    {
        DynRelation::where(function($query) use ($remove) {
            $query->where('origin_id', $this->id)
                ->where('link_id', $remove->id);
        })->orwhere(function ($query) use ($remove){
            $query->where('link_id', $this->id)
            ->where('origin_id', $remove->id);
        })->delete();
    }

    public function attach(DynamicBase|Collection $connect)
    {
        $connect instanceof Collection
            ? $connect->map(fn ($item) => $this->attachOne($item) )
            : $this->attachOne($connect);
    }

    private function attachOne(DynamicBase $connect)
    {
        DynRelation::create([
            'origin_id' => $this->id,
            'link_id' =>  $connect->id,
            'origin_type' => $this->type(),
            'link_type' => $connect->type(),       
        ]);
    }

    public function sync($type, Collection $sync)
    {
        $original = $this->relation($type)->pluck('id');

        $this->detach(
            $this->getTypes(
                DynHead::Wherein('id', $original->diff($sync->pluck('id')))->get()
            )
        );
        
        $this->attach(
            $this->getTypes(
                DynHead::Wherein('id', $sync->pluck('id')->diff($original))->get()
            )
        );

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
        return $this->relationships()->mapWithKeys(
            function($content, $relationship) 
            {
                return [
                    $relationship => [ 
                        'items' => DynHead::where('dyn_type', $relationship)->get()
                        ->mapWithKeys( 
                            function ($head) { 
                                $item = $head->getType();
                                return [
                                    $item->id => $item->{isset($content['name']) ? $content['name'] : 'head'} 
                                ];
                            }
                        ), 
                        'selected' => $this->relation($relationship)->pluck('id'),                            
                        'component' => isset($content['component']) ? $content['component'] : $this->relationComponent,
                    ],
                ];
            }
        );
    }

}