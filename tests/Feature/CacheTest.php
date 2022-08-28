<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use TheRiptide\LaravelDynamicDashboard\Types\TestType;

class CacheTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_cache_type()
    {
        $test = (new TestType)->factory()->create();

        
    }
    

}
