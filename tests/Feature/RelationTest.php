<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Dyndash\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use TheRiptide\LaravelDynamicDashboard\Types\TestType;
use TheRiptide\LaravelDynamicDashboard\Models\DynRelation;

class RelationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_sync_one_type_to_another()
    {
        $one = (new TestType())->factory()->create();
        $two = (new TestType())->factory()->create();

        $one->attach($two->id);

        $relation = DynRelation::first();

        $this->assertEquals($relation->origin_id, $one->id);
        $this->assertEquals($relation->link_id, $two->id);
        $this->assertEquals($relation->origin_type, $one->type());
        $this->assertEquals($relation->link_type, $two->type());
    }    

    /** @test */
    public function can_sync_one_type_to_two_others()
    {
        $one = (new TestType())->factory()->create();
        $two = (new TestType())->factory()->create();
        $three = (new TestType())->factory()->create();

        $one->attach(collect([$two->id, $three->id]));

        $relation = DynRelation::first();

        $this->assertEquals($relation->origin_id, $one->id);
        $this->assertEquals($relation->origin_type, $one->type());
        $this->assertEquals($relation->link_id, $two->id);
        $this->assertEquals($relation->link_type, $two->type());

        $relation = DynRelation::find(2);

        $this->assertEquals($relation->origin_id, $one->id);
        $this->assertEquals($relation->origin_type, $one->type());
        $this->assertEquals($relation->link_id, $three->id);
        $this->assertEquals($relation->link_type, $three->type());
    }    

    /** @test */
    public function can_grab_one_relation()
    {
        $one = (new TestType())->factory()->create();
        $two = (new TestType())->factory()->create();

        $one->attach($two->id);

        $relation = $one->relation($one->type())->first();

        $this->assertEquals($relation->id, $two->id);
        $this->assertEquals($relation->head, $two->head);
    }

    /** @test */
    public function can_grab_one_relation_reverse()
    {
        $one = (new TestType())->factory()->create();
        $two = (new TestType())->factory()->create();

        $two->attach($one->id);

        $relation = $one->relation($one->type())->First();

        $this->assertEquals($relation->id, $two->id);
        $this->assertEquals($relation->head, $two->head);
    }

    /** @test */
    public function can_grab_multiple_relations()
    {
        $one = (new TestType())->factory()->create();
        $two = (new TestType())->factory()->create();
        $three = (new TestType())->factory()->create();

        $one->attach([$two->id, $three->id]);

        $relation = $one->relation($one->type());

        $this->assertEquals($relation[0]->id, $two->id);
        $this->assertEquals($relation[0]->head, $two->head);

        $this->assertEquals($relation[1]->id, $three->id);
        $this->assertEquals($relation[1]->head, $three->head);
    }

    /** @test */
    public function can_grab_multiple_relations_reverse()
    {
        $one = (new TestType())->factory()->create();
        $two = (new TestType())->factory()->create();
        $three = (new TestType())->factory()->create();

        $two->attach([$one->id, $three->id]);
        $three->attach([$one->id, $two->id]);

        $relation = $one->relation($one->type());

        $this->assertEquals($relation[0]->id, $two->id);
        $this->assertEquals($relation[0]->head, $two->head);

        $this->assertEquals($relation[1]->id, $three->id);
        $this->assertEquals($relation[1]->head, $three->head);
    }

    /** @test */
    public function can_detach_one_type_from_another()
    {

        $one = (new TestType())->factory()->create();
        $two = (new TestType())->factory()->create();
        $three = (new TestType())->factory()->create();

        $one->attach([$two->id, $three->id]);

        $one->detach($three->id);

        $this->assertCount(1, DynRelation::get());

        $relation = DynRelation::first();

        $this->assertEquals($relation->origin_id, $one->id);
        $this->assertEquals($relation->origin_type, $one->type());
        $this->assertEquals($relation->link_id, $two->id);
        $this->assertEquals($relation->link_type, $two->type());
    }    

    /** @test */
    public function can_detach_several_types_from_another()
    {
        $one = (new TestType())->factory()->create();
        $two = (new TestType())->factory()->create();
        $three = (new TestType())->factory()->create();
        $four = (new TestType())->factory()->create();

        $one->attach([$two->id, $three->id, $four->id]);

        $one->detach([$three->id, $four->id]);

        $this->assertCount(1, DynRelation::get());

        $relation = DynRelation::first();

        $this->assertEquals($relation->origin_id, $one->id);
        $this->assertEquals($relation->origin_type, $one->type());
        $this->assertEquals($relation->link_id, $two->id);
        $this->assertEquals($relation->link_type, $two->type());
    }        

    /** @test */
    public function can_use_sync_to_add_and_remove_model_relationship()
    {
        $one = (new TestType())->factory()->create();
        $two = (new TestType())->factory()->create();
        $three = (new TestType())->factory()->create();
        $four = (new TestType())->factory()->create();

        $one->attach($two->id);

        $relation = $one->relation('TestType')->pluck('id');

        $this->assertTrue($relation->contains($two->id));
        $this->assertFalse($relation->contains($three->id));
        $this->assertFalse($relation->contains($four->id));

        $one->sync('TestType', [$three->id, $four->id]);

        $sync = $one->relation('TestType')->pluck('id');

        $this->assertFalse($sync->contains($two->id));
        $this->assertTrue($sync->contains($three->id));
        $this->assertTrue($sync->contains($four->id));
    }

    /** @test */
    public function can_use_function_to_pull_data_into_dashboard()
    {
        $one = (new TestType())->factory()->create();
        $two = (new Article())->factory()->create();
        $three = (new Article())->factory()->create();

        $one->attach($two->id);

        $response = $one->getRelationshipsForDashboard('Article');

        $this->assertTrue($response['Article']->items->contains($two->head));
        $this->assertTrue($response['Article']->items->contains($three->head));
        $this->assertTrue($response['Article']->selected->contains($two->id));
    }
}
