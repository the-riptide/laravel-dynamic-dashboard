<?php

namespace TheRiptide\LaravelDynamicDashboard\Models;

use Illuminate\Support\Str;
use TheRiptide\LaravelDynamicDashboard\Models\DynBase;

class DynHead extends DynBase
{


    protected $guarded = [];

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

