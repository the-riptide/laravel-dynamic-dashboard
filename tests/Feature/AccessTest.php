<?php

namespace TheRiptide\LaravelDynamicDashboard\Tests\Feature;

use App\Dyndash\Service;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use TheRiptide\LaravelDynamicDashboard\Http\Livewire\DashboardIndex;
use TheRiptide\LaravelDynamicDashboard\Http\Livewire\DashboardManage;
use TheRiptide\LaravelDynamicDashboard\Tests\TestCase;

class AccessTest extends TestCase
{
    #[Test]
    public function guests_and_users_off_the_allow_list_are_refused(): void
    {
        $service = (new Service)->create(['head' => 'Keep me']);
        $urls = ['/dashboard/service', '/dashboard/create/service', "/dashboard/edit/{$service->id}/service"];

        foreach ($urls as $url) {
            $this->get($url)->assertForbidden();
        }

        $this->actingAs($this->user('stranger@example.com'));
        foreach ($urls as $url) {
            $this->get($url)->assertForbidden();
        }
    }

    #[Test]
    public function the_pages_render_in_the_layout_with_their_titles_and_bindings(): void
    {
        $this->actingAs($this->user());
        $service = (new Service)->create(['head' => 'Timer training', 'primary_image' => 'staticImages/x.jpg']);

        $this->get('/dashboard/service')
            ->assertOk()
            ->assertSeeLivewire('dashboard-index')
            ->assertSeeInOrder(['<h3', 'Services', '</h3>'], false)
            ->assertSee('Timer training')
            ->assertSee(route('dyndash.edit', ['type' => 'service', 'id' => $service->id]))
            ->assertSee("deleteId = {$service->id}", false)
            ->assertSee(route('dyndash.index', ['event']))
            ->assertSee(route('logout'));

        $this->get("/dashboard/edit/{$service->id}/service")
            ->assertOk()
            ->assertSeeLivewire('dashboard-manage')
            ->assertSeeInOrder(['<h3', 'Manage Service', '</h3>'], false)
            ->assertSee('wire:model="values.head"', false)
            ->assertSee('wire:model="values.neck"', false)
            ->assertSee('wire:model="values.color"', false)
            ->assertSee(".entangle('values.body')", false)
            ->assertSee('wire:model="values.primary_image"', false)
            ->assertSee("removeImage('primary_image')", false)
            ->assertSee('wire:submit="save"', false);

        $this->get('/dashboard/create/experience')
            ->assertOk()
            ->assertSee('Manage Experience');
    }

    #[Test]
    public function unknown_types_and_ids_are_404s(): void
    {
        $this->actingAs($this->user());

        $this->get('/dashboard/no_such_type')->assertNotFound();
        $this->get('/dashboard/create/no_such_type')->assertNotFound();
        $this->get('/dashboard/edit/999999/service')->assertNotFound();
        $this->get('/dashboard/edit/no-such-slug/service')->assertNotFound();
    }

    #[Test]
    public function records_can_still_be_opened_by_slug(): void
    {
        $this->actingAs($this->user());
        (new Service)->create(['head' => 'Timer training']);

        $this->get('/dashboard/edit/timer-training/service')->assertOk()->assertSee('Timer training');
    }

    #[Test]
    public function save_delete_and_remove_image_refuse_users_off_the_allow_list(): void
    {
        $service = (new Service)->create(['head' => 'Keep me', 'primary_image' => 'staticImages/x.jpg']);
        $this->actingAs($this->user('stranger@example.com'));

        Livewire::test(DashboardIndex::class, ['type' => 'service'])
            ->set('deleteId', $service->id)
            ->call('delete')
            ->assertForbidden();
        $this->assertDatabaseHas('dyn_heads', ['id' => $service->id]);

        Livewire::test(DashboardManage::class, ['type' => 'service', 'id' => (string) $service->id])
            ->set('values.head', 'Changed')
            ->call('save')
            ->assertForbidden();

        Livewire::test(DashboardManage::class, ['type' => 'service', 'id' => (string) $service->id])
            ->call('removeImage', 'primary_image')
            ->assertForbidden();

        $fresh = (new Service)->find((string) $service->id, false);
        $this->assertSame('Keep me', $fresh->head);
        $this->assertSame('staticImages/x.jpg', $fresh->primary_image);
    }
}
