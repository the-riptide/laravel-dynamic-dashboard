@props(['model'])

<div>
    <label class="block text-sm font-medium text-gray-700"> {{Str::ucfirst($model->title)}} </label>
    
    <input
        wire:model.defer="{{$model->model}}"
        type="file" 
    >
    
    @error($model->model) 
        <span>{{ $message }}</span> 
    @enderror
</div>