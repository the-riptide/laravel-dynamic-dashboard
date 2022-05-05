@props(['model'])

<div class="h-5">

    <label class="text-sm font-medium text-gray-700"> {{Str::ucfirst($model->title)}} </label>
    <input wire:model.defer="{{$model->model}}"  type="checkbox" class="content-end justify-left focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">

</div> 
