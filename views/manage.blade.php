<div>
    <div 
        class="w-fill mt-5 py-5 shadow sm:rounded sm:overflow-hidden overflow-visible"
    >

        <form wire:submit.prevent="save">
            
            <h3 class="text-xl py-3 text-center font-medium text-gray-900">
                Manage {{Str::ucfirst($type)}}
            </h3>


            <div
                class="p-6 bg-white space-y-6"
            >

                @foreach($fields as $field)

                    <x-dynamic-component :component="$field->component" :model="$field" />

                @endforeach
            
            </div>
            <x-dyndash::buttons.slot
                wire:loading.class="cursor-not-allowed opacity-50"
                type="submit"
            >
                Save
            </x-dyndash::botton.slot>
        </form>
    </div>

</div>