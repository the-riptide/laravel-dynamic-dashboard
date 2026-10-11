<?php

namespace TheRiptide\LaravelDynamicDashboard\Traits;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use TheRiptide\LaravelDynamicDashboard\Models\DynHead;
use TheRiptide\LaravelDynamicDashboard\Objects\DynamicBase;

trait GetType
{
    public function getType($type, DynHead|string|null $head = null, $getCache = true): DynamicBase
    {
        if ($type == 'TestType') {
            return $this->setModel('TheRiptide\LaravelDynamicDashboard\Types\TestType', $head);
        }

        $model = $this->dynamicTypeClass($type);
        if (class_exists($model)) {
            return $this->setModel($model, $head, $getCache);
        }

        throw new Exception('Not an existing type', 1);
    }

    /** For the dashboard routes: a type or id that does not exist is a 404. Bypasses the cache, like the dashboard always did. */
    public function getTypeOrAbort(string $type, ?string $id = null): DynamicBase
    {
        $model = $type == 'TestType' ? 'TheRiptide\LaravelDynamicDashboard\Types\TestType' : $this->dynamicTypeClass($type);

        abort_unless(class_exists($model), 404);

        return $id === null
            ? (new $model)->new()
            : ((new $model)->find($id, false) ?? abort(404));
    }

    public function getTypes(Collection $heads)
    {
        return $heads->map(fn ($head) => $head->getType());
    }

    public function getAllTypes()
    {
        $files = File::allFiles(app_path(config('dyndash.folder')));

        return collect($files)
            ->map(
                fn ($file) => Str::before($file->getBasename(), '.')
            );
    }

    private function dynamicTypeClass(string $type): string
    {
        return 'App\\'.config('dyndash.folder').'\\'.Str::of($type)->camel()->ucfirst();
    }

    private function setModel($model, $head = null, $getCache = true)
    {
        return $head
            ? (new $model)->find($head, $getCache)
            : (new $model)->new();
    }
}
