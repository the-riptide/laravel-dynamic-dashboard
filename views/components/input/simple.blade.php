@props(['model'])

<div>
    <x-dyndash::input.label>{{ Str::ucfirst($model->title) }}</x-dyndash::input.label>
    <input
        class="@error($model->model) border-red-500 @enderror h-10 w-full rounded-lg border border-gray-300 bg-white px-5 pr-16 text-sm focus:outline-none focus:ring-indigo-900"
        wire:model.defer="{{ $model->name }}" type="{{ $model->type }}" placeholder="{{ $model->placeholder }}">
    @error($model->model)
        <span class="block pt-2 text-sm text-red-500">{{ $message }}</span>
    @enderror
</div>
