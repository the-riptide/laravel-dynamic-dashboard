<?php

namespace TheRiptide\LaravelDynamicDashboard\Tools;

use TheRiptide\LaravelDynamicDashboard\Models\DynRelation;

trait hasRelations 
{

    protected function related($type)
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