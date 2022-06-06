<?php

namespace TheRiptide\LaravelDynamicDashboard\Models;

use Illuminate\Support\Str;
use TheRiptide\LaravelDynamicDashboard\Models\DynBase;

class DynHead extends DynBase
{

    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($post) 
        {
            $post->update([
                'dyn_order' => DynHead::count() == 0 
                    ? 1 
                    : DynHead::where('dyn_type', $post->dyn_type)->max('dyn_order') + 1
                ]
            ); 
        });
    }

    public function setSlug($text) {

        $text = Str::of($text)->words(4)->slug('-')->__toString();
        $count = (new $this)->where('slug', 'like', $text . '%')->count();

        $this->slug = ($count === 0)
            ? $text
            : $text . '-' . $count;
    }

    public function deleteAll() {

        $this->deleteNext();

        $this->delete();
    }
}

