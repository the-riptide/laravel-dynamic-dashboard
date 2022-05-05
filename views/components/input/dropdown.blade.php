@props(['model'])

<div 
    x-data="{
        items : {{json_encode($model->items)}},
        selected : {{json_encode($model->content)}}
    }">
    <label class="text-sm font-medium text-gray-700">{{Str::ucfirst($model->title)}}</label>

    <select  wire:model.defer="{{$model->model}}" 
        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        <option> Please Choose... </option>

        <template x-for="(item, index) in items" :key="index">
            <option 
                x-text="item"
                :value="index"
                :selected="index == selected"
            ></option>
        </template>

    </select>

    @error($model->model)  
        <span>{{ $message }}</span> 
    @enderror

</div>
