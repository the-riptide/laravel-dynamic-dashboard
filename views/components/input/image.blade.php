@props(['model'])

<div class="max-w-md">
    <label class="mb-2 block text-base font-medium text-gray-700"> {{ Str::ucfirst($model->title) }} </label>
    <div class="@error($model->model) border-red-500 @enderror rounded-md border border-gray-300 bg-white p-8">
        {{-- Image Preview --}}
        @isset($model['content'])
            <img src="{{ Storage::url($model['content']) }}" class="mb-8 w-full rounded-md" alt="">
        @endisset

        {{-- Image upload --}}
        <input
            wire:model.defer="{{ $model->model }}"
            type="file">

        @error($model->model)
            <span class="block pt-2 text-red-500">{{ $message }}</span>
        @enderror
    </div>
</div>
