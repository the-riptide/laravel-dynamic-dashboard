@props(['model'])

<div>
    <label class="block text-sm font-medium text-gray-700"> {{Str::ucfirst($model->title)}} </label>
    <input
        class="border border-gray-300 bg-white h-10 w-full px-5 pr-16 rounded-lg text-sm focus:outline-none focus:ring-indigo-900"
        wire:model.defer="{{$model->name}}"
        type="{{$model->type}}"
        placeholder="{{$model->placeholder}}"
    >
    @error($model->model) 
        <span>{{ $message }}</span> 
    @enderror
</div>