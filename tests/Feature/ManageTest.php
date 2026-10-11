<?php

namespace TheRiptide\LaravelDynamicDashboard\Tests\Feature;

use App\Dyndash\Event;
use App\Dyndash\Experience;
use App\Dyndash\Service;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use TheRiptide\LaravelDynamicDashboard\Http\Livewire\DashboardIndex;
use TheRiptide\LaravelDynamicDashboard\Http\Livewire\DashboardManage;
use TheRiptide\LaravelDynamicDashboard\Tests\TestCase;

class ManageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs($this->user());
    }

    #[Test]
    public function creating_requires_the_declared_fields_and_the_image_with_the_usual_wording(): void
    {
        Livewire::test(DashboardManage::class, ['type' => 'service'])
            ->call('save')
            ->assertHasErrors(['values.head' => 'required', 'values.neck' => 'required', 'values.body' => 'required', 'values.primary_image' => 'required'])
            ->assertHasNoErrors(['values.secondary_image', 'values.primary_image_copyright'])
            ->assertSee('The primary image field is required.')
            ->assertSee('The head field is required.');

        $this->assertDatabaseCount('dyn_heads', 0);
    }

    #[Test]
    public function creating_stores_it_derives_the_slug_and_resizes_the_image_below_its_width(): void
    {
        $this->createService('Chip timing for trail runs', UploadedFile::fake()->image('hero.jpg', 1000, 600))
            ->assertHasNoErrors()
            ->assertRedirect(route('dyndash.index', ['service']))
            ->assertSessionHas('status', 'service saved successfully!');

        $service = (new Service)->get()->first();
        $this->assertSame('Chip timing for trail runs', $service->head);
        $this->assertSame('navy', $service->color);
        // The first four words of the first field.
        $this->assertSame('chip-timing-for-trail', $service->slug);
        $this->assertDatabaseHas('dyn_heads', ['dyn_type' => 'Service', 'slug' => 'chip-timing-for-trail', 'dyn_order' => 1]);
        $this->assertStringStartsWith('dynImages/', $service->primary_image);

        // A variant for every configured width strictly below the source width (1000px).
        $name = basename($service->primary_image);
        $this->assertFileExists(storage_path("app/public/{$service->primary_image}"));
        $this->assertSame(500, getimagesize(storage_path("app/public/dynImages/smallest/{$name}"))[0]);
        $this->assertSame(800, getimagesize(storage_path("app/public/dynImages/small/{$name}"))[0]);
        $this->assertSame(IMAGETYPE_JPEG, getimagesize(storage_path("app/public/dynImages/small/{$name}"))[2]);
        $this->assertFileDoesNotExist(storage_path("app/public/dynImages/smallish/{$name}"));

        // srcset() lists the variants in ascending size, as a Stringable with a trailing ", ".
        $this->assertSame(
            "/storage/dynImages/smallest/{$name} 500w, /storage/dynImages/small/{$name} 800w, ",
            (string) srcset($service, 'primary_image')
        );
    }

    #[Test]
    public function png_uploads_get_png_variants(): void
    {
        $this->createService('Transparent logo', UploadedFile::fake()->image('logo.png', 1200, 400))->assertHasNoErrors();

        $service = (new Service)->get()->first();
        $variant = getimagesize(storage_path('app/public/dynImages/smallish/'.basename($service->primary_image)));
        $this->assertSame([1080, 360, IMAGETYPE_PNG], [$variant[0], $variant[1], $variant[2]]);
    }

    #[Test]
    public function editing_keeps_the_slug_and_the_existing_image(): void
    {
        $service = $this->service(['head' => 'Timer training', 'primary_image' => 'staticImages/swiss-epic-01.jpg']);

        Livewire::test(DashboardManage::class, ['type' => 'service', 'id' => (string) $service->id])
            ->assertSet('values.head', 'Timer training')
            ->assertSet('values.primary_image', 'staticImages/swiss-epic-01.jpg')
            ->set('values.head', 'Timer training, renamed')
            ->set('values.neck', 'New summary.')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('dyndash.index', ['service']));

        // Through the cache, as the site reads it.
        $fresh = (new Service)->find((string) $service->id);
        $this->assertSame('Timer training, renamed', $fresh->head);
        $this->assertSame('New summary.', $fresh->neck);
        $this->assertSame('timer-training', $fresh->slug);
        $this->assertSame('staticImages/swiss-epic-01.jpg', $fresh->primary_image);
        $this->assertDatabaseCount('dyn_heads', 1);
    }

    #[Test]
    public function relations_are_synced_from_the_form_and_seen_from_both_ends(): void
    {
        $biking = (new Experience)->create(['head' => 'Mountain biking']);
        $swissEpic = $this->event('Swiss Epic');
        $capeEpic = $this->event('Cape Epic');

        Livewire::test(DashboardManage::class, ['type' => 'experience', 'id' => (string) $biking->id])
            ->assertSet('relations.rel_event', [])
            ->set('relations.rel_event', [(string) $swissEpic->id, (string) $capeEpic->id])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertEqualsCanonicalizing(
            ['Swiss Epic', 'Cape Epic'],
            (new Experience)->find((string) $biking->id, false)->relation('Event')->pluck('name')->all()
        );
        $this->assertSame(['Mountain biking'], $swissEpic->relation('Experience')->pluck('head')->all());

        Livewire::test(DashboardManage::class, ['type' => 'experience', 'id' => (string) $biking->id])
            ->assertSet('relations.rel_event', fn ($selected) => collect($selected)->sort()->values()->all() === collect([$swissEpic->id, $capeEpic->id])->sort()->values()->all())
            ->set('relations.rel_event', [(string) $capeEpic->id])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(['Cape Epic'], (new Experience)->find((string) $biking->id, false)->relation('Event')->pluck('name')->all());
        $this->assertDatabaseCount('dyn_relations', 1);
    }

    #[Test]
    public function deleting_from_the_index_removes_the_head_and_its_fields(): void
    {
        $keep = $this->service(['head' => 'Keep']);
        $drop = $this->service(['head' => 'Drop']);

        Livewire::test(DashboardIndex::class, ['type' => 'service'])
            ->set('deleteId', $drop->id)
            ->call('delete');

        $this->assertDatabaseMissing('dyn_heads', ['id' => $drop->id]);
        $this->assertDatabaseMissing('dyn_strings', ['dyn_head_id' => $drop->id]);
        $this->assertDatabaseMissing('dyn_texts', ['dyn_head_id' => $drop->id]);
        $this->assertDatabaseHas('dyn_heads', ['id' => $keep->id]);
        $this->assertSame(['Keep'], (new Service)->get(false)->pluck('head')->all());
    }

    #[Test]
    public function reordering_moves_an_item_and_shifts_the_others(): void
    {
        $this->service(['head' => 'A']);
        $this->service(['head' => 'B']);
        $this->service(['head' => 'C']);

        Livewire::test(DashboardIndex::class, ['type' => 'service'])
            ->call('setOrderEvent', 1, 3)
            ->assertSet('openOrder', false);

        $this->assertSame(['B', 'C', 'A'], (new Service)->get(false)->pluck('head')->all());
        $this->assertSame(['B', 'C', 'A'], (new Service)->get()->pluck('head')->all());

        Livewire::test(DashboardIndex::class, ['type' => 'service'])->call('setOrderEvent', 3, 1);

        $this->assertSame(['A', 'B', 'C'], (new Service)->get(false)->pluck('head')->all());
        $this->assertSame(['A', 'B', 'C'], (new Service)->get()->pluck('head')->all());
    }

    #[Test]
    public function removing_an_image_deletes_the_original_and_every_variant(): void
    {
        $this->createService('Chip timing', UploadedFile::fake()->image('hero.jpg', 1000, 600));
        $service = (new Service)->get()->first();
        $name = basename($service->primary_image);

        Livewire::test(DashboardManage::class, ['type' => 'service', 'id' => (string) $service->id])
            ->call('removeImage', 'primary_image')
            ->assertSet('values.primary_image', null);

        $this->assertFileDoesNotExist(storage_path("app/public/{$service->primary_image}"));
        $this->assertFileDoesNotExist(storage_path("app/public/dynImages/smallest/{$name}"));
        $this->assertFileDoesNotExist(storage_path("app/public/dynImages/small/{$name}"));
        $this->assertNull((new Service)->find((string) $service->id)->primary_image);
    }

    #[Test]
    public function saving_after_removing_an_image_leaves_the_field_empty(): void
    {
        $this->createService('Chip timing', UploadedFile::fake()->image('hero.jpg', 1000, 600));
        $service = (new Service)->get()->first();

        // Before the upgrade, the save wrote the deleted path back.
        Livewire::test(DashboardManage::class, ['type' => 'service', 'id' => (string) $service->id])
            ->call('removeImage', 'primary_image')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertNull((new Service)->find((string) $service->id, false)->primary_image);
        $this->assertNull((new Service)->find((string) $service->id)->primary_image);
    }

    #[Test]
    public function svg_uploads_are_stored_without_variants_and_small_images_get_none(): void
    {
        $svg = UploadedFile::fake()->createWithContent('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"/>');
        $small = UploadedFile::fake()->image('small.jpg', 500, 300);

        foreach ([['Vector event', $svg], ['Small event', $small]] as [$name, $file]) {
            Livewire::test(DashboardManage::class, ['type' => 'event'])
                ->set('values.name', $name)
                ->set('values.featured', false)
                ->set('values.link', 'https://example.com')
                ->set('values.logo', $file)
                ->call('save')
                ->assertHasNoErrors();
        }

        $events = (new Event)->get(false);
        $this->assertStringEndsWith('.svg', $events->first()->logo);
        foreach ($events as $event) {
            $this->assertFileExists(storage_path("app/public/{$event->logo}"));
            $this->assertFileDoesNotExist(storage_path('app/public/dynImages/smallest/'.basename($event->logo)));
            $this->assertSame('', (string) srcset($event, 'logo'));
        }
    }

    private function createService(string $head, UploadedFile $image)
    {
        return Livewire::test(DashboardManage::class, ['type' => 'service'])
            ->set('values.head', $head)
            ->set('values.neck', 'Rugged kit for rugged courses.')
            ->set('values.color', 'navy')
            ->set('values.body', '<p>We time trail runs.</p>')
            ->set('values.primary_image', $image)
            ->call('save');
    }

    private function service(array $values): Service
    {
        return (new Service)->create(['neck' => 'Summary', 'color' => 'gold', 'body' => '<p>Body</p>', 'primary_image' => 'staticImages/x.jpg', ...$values]);
    }

    private function event(string $name): Event
    {
        return (new Event)->create(['name' => $name, 'featured' => false, 'link' => 'https://example.com', 'logo' => 'staticImages/logo.svg']);
    }
}
