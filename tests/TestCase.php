<?php

namespace TheRiptide\LaravelDynamicDashboard\Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\TestCase as Orchestra;
use TheRiptide\LaravelDashboardComponents\ComponentServiceProvider;
use TheRiptide\LaravelDynamicDashboard\DynamicDashboardServiceProvider;

#[WithMigration]
abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    /** DynImage writes real files under storage_path(); each test gets its own. */
    protected string $storage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->storage);

        parent::tearDown();
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            ComponentServiceProvider::class,
            DynamicDashboardServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        $this->storage = sys_get_temp_dir().'/dyndash-storage-'.getmypid();
        foreach (['app/public', 'framework/cache', 'framework/views'] as $dir) {
            File::ensureDirectoryExists($this->storage.'/'.$dir);
        }
        $app->useStoragePath($this->storage);

        // The types under tests/Fixtures/App/Dyndash, as a host app has them under app/Dyndash.
        $app->useAppPath(__DIR__.'/Fixtures/App');
        // Blade's component resolver reads the namespace from composer.json for app_path(); there is none for the fixtures.
        (fn () => $this->namespace = 'App\\')->call($app);

        $app['config']->set('filesystems.disks.public.root', $this->storage.'/app/public');
        $app['config']->set('filesystems.disks.public.url', '/storage');
        $app['config']->set('filesystems.disks.local.root', $this->storage.'/app/private');
        $app['config']->set('dyndash.emails', ['editor@example.com']);
        // The default config links the dynamic-text page, which is not installed here.
        $app['config']->set('dyndash.menu.items', []);
    }

    protected function defineRoutes($router)
    {
        // The dashboard menu posts to the host app's logout route.
        $router->post('logout', fn () => '')->name('logout');
    }

    protected function user(string $email = 'editor@example.com'): User
    {
        return User::factory()->create(['email' => $email]);
    }
}
