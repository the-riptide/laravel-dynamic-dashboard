<div>

    <form wire:submit.prevent="save">

        {{-- Header --}}
        @section('title')
            Manage {{ Str::ucfirst($type) }}
        @endsection

        {{-- Input Fields --}}
        <div class="space-y-6">
            @foreach ($fields as $field)
                <x-dynamic-component :component="$field->component" :model="$field" />
            @endforeach
        </div>

        {{-- Buttons --}}
        <div class="pt-8">
            <x-dyndash::buttons.slot wire:loading.class="cursor-not-allowed opacity-50" type="submit">
                Save
                </x-dyndash::botton.slot>
        </div>

    </form>


</div>
