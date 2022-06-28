<?php 

namespace TheRiptide\LaravelDynamicDashboard\Tools;

use Illuminate\Support\Facades\Cache;

trait UseCache {

    protected function existCache($type, $id)
    {
        return Cache::has($this->cachePath($type, $id));
    }

    protected function firstCache($type, $id)
    {
        return Cache::Get($this->cachePath($type, $id));
    }

    protected function putCache($type, $id, $item)
    {
        Cache::put($this->cachePath($type, $id), $item, now()->addHours(6) );        
    }

    private function cachePath($type, $id)
    {
        return 'Dyn-' . $type . ':' . $id;
    }
}