<?php

namespace TheRiptide\LaravelDynamicDashboard\Tools;

use Illuminate\Support\Collection;

use function PHPUnit\Framework\isInstanceOf;
use TheRiptide\LaravelDynamicDashboard\Models\DynRelation;
use TheRiptide\LaravelDynamicDashboard\Objects\DynamicBase;

trait hasRelations 
{

    public function sync(DynamicBase|Collection $connect)
    {
        $connect instanceof Collection
            ? $connect->map(fn ($item) => $this->syncOne($item) )
            : $this->syncOne($connect);
    }

    private function syncOne(DynamicBase $connect)
    {
        DynRelation::create([
            'origin_id' => $this->id,
            'link_id' =>  $connect->id,
            'origin_type' => $this->type(),
            'link_type' => $connect->type(),
        
        ]);
    }
    
    public function related($type)
    {
        return DynRelation::where(
            function($query) use ($type) {
                $query->where('link_id', $this->id)
                    ->where('link_type', $type);
            }
        )->orwhere(
            function($query) use ($type){
                $query->where('origin_id', $this->id)
                    ->where('origin_type', $type);
            }
        )->get();
    }

}