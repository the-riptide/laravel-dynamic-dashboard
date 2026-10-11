<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Orchestra\Testbench\Factories\UserFactory;

/** DynHeadFactory creates its user through the host app's App\Models\User. */
class User extends Authenticatable
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return new class extends UserFactory
        {
            protected $model = User::class;
        };
    }
}
