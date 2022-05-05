<?php

namespace TheRiptide\LaravelDynamicDashboard\Models;

use App\Dynamic\Front\Full;
use Illuminate\Support\Str;
use TheRiptide\LaravelDynamicDashboard\Models\DynBase;

class DynHead extends DynBase
{
    public function setSlug($text) {

        $text = Str::of($text)->words(4)->slug('-')->__toString();
        $count = (new $this)->where('slug', 'like', $text . '%')->count();

        $this->slug = ($count === 0)
            ? $text
            : $text . '-' . $count;
    }

    public function links() : Full {

        return new Full($this);
    }

    public function deleteAll() {

        $this->deleteNext();

        $this->delete();

    }
}

