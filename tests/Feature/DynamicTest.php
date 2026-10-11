<?php

namespace TheRiptide\LaravelDynamicDashboard\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TheRiptide\LaravelDynamicDashboard\Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use TheRiptide\LaravelDynamicDashboard\Models\DynHead;
use TheRiptide\LaravelDynamicDashboard\Models\DynText;
use TheRiptide\LaravelDynamicDashboard\Models\DynString;

class DynamicTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    #[DataProvider('componentList')]
    public function can_connect_components($component)
    {
        $head = DynHead::factory()->create();
        $text = (new $component)->factory()->create();

        $head = $head->conNext($text);
        
        $this->assertEquals($head->next_model_id, $text->id);
        $this->assertEquals($head->next_model, class_basename($text));
    }

    public static function componentList()
    {
        return [
            'string' => [DynString::class],
            'text' => [DynText::class],
        ];
    }

    #[Test]
    public function can_connect_multiple_components()
    {
        $head = DynHead::factory()->create();
        $textOne = DynText::factory()->create();
        $textTwo = DynText::factory()->create();

        $head = $head->conNext($textOne);
        $head->save();
        $textOne = $textOne->conNext($textTwo);
        $textOne->save();

        $model = $head->getNext();

        $this->assertEquals($model->next_model_id, $textTwo->id);
        $this->assertEquals($model->next_model, class_basename($textTwo));
    }

    #[Test]
    public function can_use_get_all_to_get_all_components() {

        $head = DynHead::factory()->create();
        $textOne = DynText::factory()->create();
        $textTwo = DynText::factory()->create();

        $head = $head->conNext($textOne);
        $head->save();
        $textOne = $textOne->conNext($textTwo);
        $textOne->save();

        $model = $head->getAll();

        $this->assertEquals($model[0]->slug, $head->slug);
        $this->assertEquals($model[1]->name, $textOne->name);
        $this->assertEquals($model[2]->name, $textTwo->name);
    }

    #[Test]
    public function delete_all_on_head_deletes_all_models()
    {
        $head = DynHead::factory()->create();
        $textOne = DynText::factory()->create();
        $textTwo = DynText::factory()->create();

        $head = $head->conNext($textOne);
        $head->save();
        $textOne = $textOne->conNext($textTwo);
        $textOne->save();

        $this->assertEquals(DynHead::count(), 1);
        $this->assertEquals(DynText::count(), 2);

        $head->deleteAll();

        $this->assertEquals(DynHead::count(), 0);
        $this->assertEquals(DynText::count(), 0);


    }
    #[Test]
    public function can_get_text_by_name()
    {
        $head = DynHead::factory()->create();
        $text = DynText::factory()->create();

        $head = $head->conNext($text);

        $model = $head->getNext();

        $this->assertEquals($text->content, $model->content);
        $this->assertEquals($text->name, $model->name);
    }

    #[Test]
    public function can_unset_temporary_attributes_on_dynamic_model(){

        $text = DynText::Factory()->create();

        $text->randomAttribute = 'fleeble';

        $response = $text->unsetTempAttributes();

        $this->assertEquals(null, $response->randomAttribute);
        $this->assertEquals($text->content, $response->content);
    }
}
