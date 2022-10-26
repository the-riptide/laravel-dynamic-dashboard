<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use TheRiptide\LaravelDynamicDashboard\Models\DynHead;
use TheRiptide\LaravelDynamicDashboard\Models\DynText;
use TheRiptide\LaravelDynamicDashboard\Types\TestType;
use TheRiptide\LaravelDynamicDashboard\Models\DynEditor;
use TheRiptide\LaravelDynamicDashboard\Models\DynString;
use TheRiptide\LaravelDynamicDashboard\Tools\ModifyType;

class ChangeTypeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_remove_single_models_via_modify_type_run_function()
    {
        (new TestType)->factory()->create();
        $head = DynHead::first();
        $string = DynString::factory()->create();

        $string->next_model = $head->next_model;
        $string->next_model_id = $head->next_model_id;
        $string->save();
        $string = $string->fresh();

        $head->connext($string);
        $head->save();

        $before = (new TestType)->find($head->id, false);

        $this->assertCount(5, $before->models());
        $this->assertEquals($before->{$string->name}, $string->content);
        $this->assertEquals($before->models()[0], $string);

        (new ModifyType)->run('TestType');

        $after = (new TestType)->find($head->id, false);

        $this->assertCount(4, $after->models());
        $this->assertObjectNotHasAttribute($string->name, $after);
        $this->assertNotEquals($after->models()[0], $string);
    }    

    /** @test */
    public function can_remove_two_connected_models_via_modify_type_run_function()
    {
        (new TestType)->factory()->create();
        $head = DynHead::first();
        $string = DynString::factory()->create();
        $text = DynText::factory()->create();

        $text->next_model = $head->next_model;
        $text->next_model_id = $head->next_model_id;
        $text->save();
        $text = $text->fresh();

        $head->connext($string);
        $head->save();
        $head = $head->fresh();

        $string->connext($text);
        $string->save();
        $string = $string->fresh();

        $before = (new TestType)->find($head->id, false);

        $this->assertCount(6, $before->models());
        $this->assertEquals($before->{$string->name}, $string->content);
        $this->assertEquals($before->models()[0], $string);
        $this->assertEquals($before->{$text->name}, $text->content);
        $this->assertEquals($before->models()[1], $text);

        (new ModifyType)->run('TestType');

        $after = (new TestType)->find($head->id, false);

        $this->assertCount(4, $after->models());
        $this->assertObjectNotHasAttribute($string->name, $after);
        $this->assertObjectNotHasAttribute($text->name, $after);
        $this->assertNotEquals($after->models()[0], $string);
        $this->assertNotEquals($after->models()[1], $text);
    }    

    /** @test */
    public function can_remove_two_unnconnected_models_via_modify_type_run_function()
    {
        (new TestType)->factory()->create();
        $head = DynHead::first();
        $string = DynString::factory()->create();

        $string->next_model = $head->next_model;
        $string->next_model_id = $head->next_model_id;
        $string->save();
        $string = $string->fresh();

        $head->connext($string);
        $head->save();
        $head = $head->fresh();

        $text = DynText::factory()->create();

        $otherString = DynString::find($string->next_model_id);

        $text->next_model = $otherString->next_model;
        $text->next_model_id = $otherString->next_model_id;
        $text->save();
        $text = $text->fresh();

        $otherString->connext($text);
        $otherString->save();
        $otherString = $otherString->fresh();

        $before = (new TestType)->find($head->id, false);

        $this->assertCount(6, $before->models());
        $this->assertEquals($before->{$string->name}, $string->content);
        $this->assertEquals($before->models()[0], $string);
        $this->assertEquals($before->{$text->name}, $text->content);
        $this->assertEquals($before->models()[2], $text);

        (new ModifyType)->run('TestType');

        $after = (new TestType)->find($head->id, false);

        $this->assertCount(4, $after->models());
        $this->assertObjectNotHasAttribute($string->name, $after);
        $this->assertObjectNotHasAttribute($text->name, $after);
        $this->assertNotEquals($after->models()[0], $string);
        $this->assertNotEquals($after->models()[2], $text);
    }    

    /** @test */
    public function can_add_single_models_to_middle_via_modify_type_run_function()
    {
        (new TestType)->factory()->create();
        $head = DynHead::first();

        $removed = $head->getAll()[1];
        $model = $head->getAll()[2];
        $head->conNext($model);
        $head->save();

        $before = (new TestType)->find($head->id, false);

        $this->assertCount(3, $before->models());
        $this->assertObjectNotHasAttribute($removed->name, $before);
        $this->assertEquals($before->models()[0], $model);

        (new ModifyType)->run('TestType');

        $after = (new TestType)->find($head->id, false);

        $this->assertCount(4, $after->models());
        $this->assertObjectHasAttribute($removed->name, $after);

        $this->assertEquals($after->models()[0]->name, $removed->name);
        $this->assertEquals($after->models()[1], $model);
    }    

    /** @test */
    public function can_add_single_models_to_end_via_modify_type_run_function()
    {
        (new TestType)->factory()->create();
        $head = DynHead::first();

        $removed = $head->getAll()[4];

        $model = $head->getAll()[3];
        $model->next_model_id = null;
        $model->next_model = null;
        $model->save();

        $before = (new TestType)->find($head->id, false);

        $this->assertCount(3, $before->models());
        $this->assertObjectNotHasAttribute($removed->name, $before);

        (new ModifyType)->run('TestType');

        $after = (new TestType)->find($head->id, false);

        $this->assertCount(4, $after->models());
        $this->assertObjectHasAttribute($removed->name, $after);

        $this->assertEquals($after->models()[2]->next_model, class_basename($removed));
    }    
    
    /** @test */
    public function can_add_two_connected_models_via_modify_type_run_funtion()
    {
        (new TestType)->factory()->create();
        $head = DynHead::first();

        $removedOne = $head->getAll()[1];
        $removedTwo = $head->getAll()[2];
        $model = $head->getAll()[3];
        $head->conNext($model);
        $head->save();

        $before = (new TestType)->find($head->id, false);

        $this->assertCount(2, $before->models());
        $this->assertObjectNotHasAttribute($removedOne->name, $before);
        $this->assertObjectNotHasAttribute($removedTwo->name, $before);
        $this->assertEquals($before->models()[0], $model);

        (new ModifyType)->run('TestType');

        $after = (new TestType)->find($head->id, false);

        $this->assertCount(4, $after->models());
        $this->assertObjectHasAttribute($removedOne->name, $after);
        $this->assertObjectHasAttribute($removedTwo->name, $after);

        $this->assertEquals($after->models()[0]->name, $removedOne->name);
        $this->assertEquals($after->models()[1]->name, $removedTwo->name);
        $this->assertEquals($after->models()[2], $model);
    }

    /** @test */
    public function can_add_two_unconnected_models_via_modify_type_run_funtion()
    {
        (new TestType)->factory()->create();
        $head = DynHead::first();

        $models = $head->getAll(); 
        $removedOne = $models[1];
        $modelOne = $models[2];
        $removedTwo = $models[3];
        $modelTwo = $models[4];
        
        $head->conNext($modelOne);
        $head->save();
        $head = $head->fresh();

        $modelOne->conNext($modelTwo);
        $modelOne->save();

        $before = (new TestType)->find($head->id, false);

        $this->assertCount(2, $before->models());
        $this->assertObjectNotHasAttribute($removedOne->name, $before);
        $this->assertObjectNotHasAttribute($removedTwo->name, $before);
        $this->assertEquals($before->models()[0]->name, $modelOne->name);
        $this->assertEquals($before->models()[1], $modelTwo);

        (new ModifyType)->run('TestType');

        $after = (new TestType)->find($head->id, false);

        $this->assertCount(4, $after->models());
        $this->assertObjectHasAttribute($removedOne->name, $after);
        $this->assertObjectHasAttribute($removedTwo->name, $after);

        $this->assertEquals($after->models()[0]->name, $removedOne->name);
        $this->assertEquals($after->models()[1]->name, $modelOne->name);
        $this->assertEquals($after->models()[2]->name, $removedTwo->name);
        $this->assertEquals($after->models()[3], $modelTwo);
    }

    /** @test */
    public function can_reorder_adjacent_models_via_modify_type_run_function()
    {
        (new TestType)->factory()->create();
        $head = DynHead::first();
        $models = $head->getAll();

        $modelOne = $models[1];
        $modelTwo = $models[2];
        $modelThree = $models[3];

        $head->connext($modelTwo);
        $head->save();

        $modelTwo->connext($modelOne);
        $modelTwo->save();
        
        $modelOne->connext($modelThree);
        $modelOne->save();
        
        $before = (new TestType)->find($head->id, false);

        $this->assertCount(4, $before->models());
        $this->assertEquals($before->models()[0], $modelTwo->fresh());
        $this->assertEquals($before->models()[1], $modelOne->fresh());

        (new ModifyType)->run('TestType');

        $after = (new TestType)->find($head->id, false);

        $this->assertCount(4, $after->models());
        $this->assertEquals($after->models()[0]->name, $modelOne->name);
        $this->assertEquals($after->models()[0]->id, $modelOne->id);
        $this->assertEquals(class_basename($after->models()[0]), class_basename($modelOne));

        $this->assertEquals($after->models()[1]->name, $modelTwo->name);
        $this->assertEquals($after->models()[1]->id, $modelTwo->id);
    }

    /** @test */
    public function can_reorder_non_connected_models_via_modify_type_run_function()
    {
        (new TestType)->factory()->create();
        $head = DynHead::first();
        $models = $head->getAll();

        $modelOne = $models[1];
        $modelTwo = $models[2];
        $modelThree = $models[3];
        $modelFour = $models[4];

        $head->connext($modelThree);
        $head->save();

        $modelThree->connext($modelTwo);
        $modelThree->save();
        
        $modelTwo->connext($modelOne);
        $modelTwo->save();
        
        $modelOne->connext($modelFour);
        $modelOne->save();

        $before = (new TestType)->find($head->id, false);

        $this->assertCount(4, $before->models());
        $this->assertEquals($before->models()[0], $modelThree->fresh());
        $this->assertEquals($before->models()[2], $modelOne->fresh());

        (new ModifyType)->run('TestType');

        $after = (new TestType)->find($head->id, false);

        $this->assertCount(4, $after->models());
        $this->assertEquals($after->models()[0]->name, $modelOne->name);
        $this->assertEquals($after->models()[0]->id, $modelOne->id);
        $this->assertEquals(class_basename($after->models()[0]), class_basename($modelOne));

        $this->assertEquals($after->models()[2]->name, $modelThree->name);
        $this->assertEquals($after->models()[2]->id, $modelThree->id);
    }

    /** @test */
    public function can_reorder_after_first_model_moved_to_end_via_modify_type_run_function()
    {
        (new TestType)->factory()->create();
        $head = DynHead::first();
        $models = $head->getAll();

        $modelOne = $models[1];
        $modelTwo = $models[2];
        $modelFour = $models[4];

        $head->connext($modelTwo);
        $head->save();

        $modelFour->connext($modelOne);
        $modelFour->save();

        $modelOne->next_model = null;
        $modelOne->next_model_id = null;
        $modelOne->save();

        $before = (new TestType)->find($head->id, false);

        $this->assertCount(4, $before->models());
        $this->assertEquals($before->models()[0], $modelTwo->fresh());
        $this->assertEquals($before->models()[3], $modelOne->fresh());

        (new ModifyType)->run('TestType');

        $after = (new TestType)->find($head->id, false);

        $this->assertCount(4, $after->models());
        $this->assertEquals($after->models()[0]->name, $modelOne->name);
        $this->assertEquals($after->models()[0]->id, $modelOne->id);
        $this->assertEquals(class_basename($after->models()[0]), class_basename($modelOne));

        $this->assertEquals($after->models()[1]->name, $modelTwo->name);
        $this->assertEquals($after->models()[1]->id, $modelTwo->id);
    }

    /** @test */
    public function can_reorder_after_an_almost_total_restructure_via_modify_type_run_function()
    {
        (new TestType)->factory()->create();
        $head = DynHead::first();
        $models = $head->getAll();

        $modelOne = $models[1];
        $modelTwo = $models[2];
        $modelThree = $models[3];
        $modelFour = $models[4];

        $head->connext($modelThree);
        $head->save();

        $modelThree->connext($modelTwo);
        $modelThree->save();

        $modelTwo->connext($modelFour);
        $modelTwo->save();

        $modelFour->connext($modelOne);
        $modelFour->save();

        $modelOne->next_model = null;
        $modelOne->next_model_id = null;
        $modelOne->save();

        $before = (new TestType)->find($head->id, false);

        $this->assertCount(4, $before->models());
        $this->assertEquals($before->models()[0], $modelThree->fresh());
        $this->assertEquals($before->models()[1], $modelTwo->fresh());
        $this->assertEquals($before->models()[2], $modelFour->fresh());
        $this->assertEquals($before->models()[3], $modelOne->fresh());

        (new ModifyType)->run('TestType');

        $after = (new TestType)->find($head->id, false);

        $this->assertCount(4, $after->models());
        $this->assertEquals($after->models()[0]->name, $modelOne->name);
        $this->assertEquals($after->models()[0]->id, $modelOne->id);
        $this->assertEquals(class_basename($after->models()[0]), class_basename($modelOne));

        $this->assertEquals($after->models()[1]->name, $modelTwo->name);
        $this->assertEquals($after->models()[1]->id, $modelTwo->id);

        $this->assertEquals($after->models()[2]->name, $modelThree->name);
        $this->assertEquals($after->models()[2]->id, $modelThree->id);

        $this->assertEquals($after->models()[3]->name, $modelFour->name);
        $this->assertEquals($after->models()[3]->id, $modelFour->id);
    }

    /** @test */
    public function can_reorder_after_a_total_restructure_via_modify_type_run_function()
    {
        (new TestType)->factory()->create();
        $head = DynHead::first();
        $models = $head->getAll();

        $modelOne = $models[1];
        $modelTwo = $models[2];
        $modelThree = $models[3];
        $modelFour = $models[4];

        $head->connext($modelThree);
        $head->save();

        $modelThree->connext($modelFour);
        $modelThree->save();

        $modelFour->connext($modelTwo);
        $modelFour->save();

        $modelTwo->connext($modelOne);
        $modelTwo->save();

        $modelOne->next_model = null;
        $modelOne->next_model_id = null;
        $modelOne->save();
        
        $before = (new TestType)->find($head->id, false);

        $this->assertCount(4, $before->models());
        $this->assertEquals($before->models()[0], $modelThree->fresh());
        $this->assertEquals($before->models()[1], $modelFour->fresh());
        $this->assertEquals($before->models()[2], $modelTwo->fresh());
        $this->assertEquals($before->models()[3], $modelOne->fresh());

        (new ModifyType)->run('TestType');

        $after = (new TestType)->find($head->id, false);

        $this->assertCount(4, $after->models());
        $this->assertEquals($after->models()[0]->name, $modelOne->name);
        $this->assertEquals($after->models()[0]->id, $modelOne->id);
        $this->assertEquals(class_basename($after->models()[0]), class_basename($modelOne));

        $this->assertEquals($after->models()[1]->name, $modelTwo->name);
        $this->assertEquals($after->models()[1]->id, $modelTwo->id);

        $this->assertEquals($after->models()[2]->name, $modelThree->name);
        $this->assertEquals($after->models()[2]->id, $modelThree->id);

        $this->assertEquals($after->models()[3]->name, $modelFour->name);
        $this->assertEquals($after->models()[3]->id, $modelFour->id);
    }

    /** @test */
    public function can_change_a_model_type_via_modify_type_run_function()
    {
        (new TestType)->factory()->create();
        $head = DynHead::first();
        $models = $head->getAll();

        $model = $models[1];

        $replacement = new DynEditor;
        $replacement->name = $model->name;
        $replacement->content = $model->content;
        $replacement->next_model = $model->next_model;
        $replacement->next_model_id = $model->next_model_id;
        $replacement->save();

        $head = $models[0];
        $head->connext($replacement);
        $head->save();

        $before = (new TestType)->find($head->id, false);

        $this->assertCount(4, $before->models());
        $this->assertEquals(class_basename($before->models()[0]), 'DynEditor');
        $this->assertEquals($before->models()[0]->name, $model->name);
        $this->assertEquals($before->models()[0]->content, $model->content);
        
        (new ModifyType)->run('TestType');

        $after = (new TestType)->find($head->id, false);

        $this->assertCount(4, $after->models());
        $this->assertEquals(class_basename($after->models()[0]), 'DynString');
        $this->assertEquals($after->models()[0]->name, $model->name);
        $this->assertEquals($after->models()[0]->content, $model->content);
    }

    /** @test */
    public function can_change_last_model_type_via_modify_type_run_function()
    {
        (new TestType)->factory()->create();
        $head = DynHead::first();
        $models = $head->getAll();

        $model = $models[4];

        $replacement = new DynEditor;
        $replacement->name = $model->name;
        $replacement->content = $model->content;
        $replacement->next_model = $model->next_model;
        $replacement->next_model_id = $model->next_model_id;
        $replacement->save();

        $previous = $models[3];
        $previous->connext($replacement);
        $previous->save();

        $before = (new TestType)->find($head->id, false);

        $this->assertCount(4, $before->models());
        $this->assertEquals(class_basename($before->models()[3]), 'DynEditor');
        $this->assertEquals($before->models()[3]->name, $model->name);
        $this->assertEquals($before->models()[3]->content, $model->content);
        
        (new ModifyType)->run('TestType');

        $after = (new TestType)->find($head->id, false);

        $this->assertCount(4, $after->models());
        $this->assertEquals(class_basename($after->models()[3]), 'DynImage');
        $this->assertEquals($after->models()[3]->name, $model->name);
        $this->assertEquals($after->models()[3]->content, $model->content);
    }
}
