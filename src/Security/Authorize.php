<?php

namespace TheRiptide\LaravelDynamicDashboard\Security;

class Authorize
{
    public function canTakeAction()
    {

        return auth()->user() && in_array(auth()->user()->email, config('dyndash.emails'))
            ? true
            : false;

    }
}
