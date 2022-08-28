<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use TheRiptide\LaravelDynamicDashboard\Models\DynHead;
use TheRiptide\LaravelDynamicDashboard\Types\TestType;

class DynamicTypeTest extends TestCase {

    use RefreshDatabase;

    /** @test */
    public function dynamic_type_create_function_works()
    {

        $article = (new TestType())->create($this->data());

        foreach ($this->data() as $key => $item) $this->assertEquals($article->$key, $item);
    }

    /** @test */
    public function dynamic_type_factory_function_works()
    {
        $article = (new TestType())->factory()->create();

        $this->assertEquals($article->slug, DynHead::first()->slug);

        foreach ($article->models() as $model) $this->assertEquals($model->content, $article->{$model->name});
    }

    /** @test */
    public function dynamic_type_create_function_works_together_with_factory()
    {
        $data = ['head' => 'Der 1. Gantrisch Loppet ist in Planung'];

        $article = (new TestType())->factory()->create($data);
        
        foreach ($article->models() as $model)
        { 
            $model->name !== 'head'
                ? $this->assertEquals($article->head, $data['head'])
                : $this->assertEquals($model->content, $article->{$model->name});
        }
    }

    /** @test */
    public function can_use_new_to_create_new_instance_of_type()
    {
        $test = new TestType;

        $new = $test->new();

        $this->assertEquals($new->head()->dyn_type, 'TestType');        

        $fields = $new->fields();

        foreach ($fields as $key => $field) $this->assertEquals($new->models()[$key]->name, $key);
    }

    /** @test */
    public function can_use_find_to_get_existing_model()
    {
        $test = (new TestType())->create($this->data());

        $article = (new TestType)->find($test->id);

        foreach ($this->data() as $key => $value ) $this->assertEquals($article->$key, $value); 
    }

    /** @test */
    public function can_use_get_to_return_a_type_collection()
    {
        $test = new TestType;

        for ($x = 0; $x < 10; $x ++) $test->factory()->create();
        
        $collection = (new TestType)->get();

        $this->assertCount(10, $collection);

        for ($x = 1; $x <= 10; $x ++) $collection[$x -1]->dyn_order == $x;
    }

    private function data()
    {
        return [
            'head' => 'Der 1. Gantrisch Loppet ist in Planung',
            'neck' => 'Medienmitteilung vom 12. April 2022, Riggisberg BE – Am 29. Januar 2023 soll der 1. Gantrisch Loppet mit Start und Ziel auf dem Gurnigel stattfinden. Die Planungen laufen bereits auf Hochtouren.',
            'body' => ' <p> Der «andere Engadiner» soll es werden, ein grosses Langlauffest für alle begeisterten Winter und Breitensportler aus dem Grossraum Gurnigel. Auf der herrlichen Loipe mit Start und Ziel bei der Stierenhütte auf dem Gurnigelpass erwartet die Teilnehmer eine 20-Kilometer-Schlaufe, welche als Einzelstarter oder im Zweierteam als Staffel absolviert werden kann. Auf den  kürzeren 12 Km können auch Anfänger sich ein erstes Mal messen. «Wir wollen ein Erlebnis für alle Langläuferinnen und Langläufer kreieren, die jeden Winter hier im Gantrischgebiet ihre Spuren ziehen», erklärt Organisator Simon Zahnd. «Das Gelände, das Panorama – es ist die perfekte Kulisse für eine solche Veranstaltung.» Er spricht da aus Erfahrung, hat er doch im Winter selbst einen Langlaufverleih in seinem Sportgeschäft in Riggisberg und ist als Langlauflehrer tätig. </p>
            <p> <b> Planungen laufen bereits</b> </p>
            <p>Es haben schon erste Gespräche mit dem Pistendienst stattgefunden, die Anmeldung ist online und die Webseite bereits als Landingpage verfügbar. «Wir müssen die Planung jetzt starten, damit wir an alle Details bis zur Erstaustragung denken können», führt Zahnd aus. Er will seine grosse Eventerfahrung einfliessen lassen, schliesslich organisiert er seit Jahren den bekannten Trailrun Gantrisch Trail. «Der Initialaufwand ist gross, doch wir engagieren uns mit viel Herz dafür, einen einmaligen Tag für alle Teilnehmerinnen und Teilnehmer auf die Beine zu stellen.» </p>',
            'image' => 'staticImages/DSC02589.jpg'
        ];

    }
    
}