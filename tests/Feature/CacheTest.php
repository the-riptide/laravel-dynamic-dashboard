<?php

namespace TheRiptide\LaravelDynamicDashboard\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use TheRiptide\LaravelDynamicDashboard\Tests\TestCase;
use TheRiptide\LaravelDynamicDashboard\Models\DynString;
use TheRiptide\LaravelDynamicDashboard\Types\TestType;

class CacheTest extends TestCase
{

    use RefreshDatabase;

    #[Test]
    public function can_cache_type()
    {
        $test = (new TestType)->factory()->create();

        $this->assertTrue(Cache::has('Dyn-TestType:'.$test->id));

        // A database edit is invisible through the cache until the type is saved again.
        DynString::where('dyn_head_id', $test->id)->where('name', 'head')->update(['content' => 'Changed']);
        $this->assertSame($test->head, (new TestType)->find((string) $test->id)->head);
        $this->assertSame('Changed', (new TestType)->find((string) $test->id, false)->head);
        $this->assertSame('Changed', (new TestType)->find((string) $test->id)->head);
    }
    

}
