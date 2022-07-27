{{-- this is a livewire view. For the livewire to work, the whole view must have a wrapping div. --}}
<div x-data="{
    openModal: false,
    deleteId: @entangle('deleteId'),
    openOrder : @entangle('openOrder'),
}">

    
    @section('title')
        {{ Str::of($type)->snake()->replace('_', ' ')->ucfirst()->plural() }}
    @endsection

    @if($canCreate)
        <x-dashcomp::buttons.href-slot href="{{ route('dyndash.create', [$type]) }}" class="mb-12">
            Create new
        </x-dashcomp::buttons.href-slot>
    @endif

    <x-dashcomp::index.table>
        <x-slot name="tableHead">
            <tr>
                @foreach ($heads as $head => $value)
                    <x-dashcomp::index.thead :title="$head" />
                @endforeach

                @if(isset($canOrder) && $canOrder)
                    <x-dashcomp::index.thead title="Set Order" />
                @endif

                <x-dashcomp::index.thead title="Actions" />

            </tr>
        </x-slot>

        @foreach ($posts as $post)
            <tr>
                @foreach ($heads as $head => $value)
                    <x-dashcomp::index.tbl-cell>
                        {{ $post->setValue($value) }}
                    </x-dashcomp::index.tbl-cell>
                @endforeach

                @if (isset($canOrder) && $canOrder)
                    <x-dashcomp::index.tbl-cell>

                    {{-- Set Order --}}
                    <div
                        x-data="{
                            order : {{@json_encode($post->dyn_order)}}, 
                            option : {{@json_encode($post->dyn_order)}},
                        }"
                    >
                        <x-dashcomp::buttons.slot 
                            x-show=" openOrder != order"
                            @click="openOrder = order"
                            class="!py-1 !px-4 !text-sm" >

                            {{$loop->index + 1}}
                        </x-dashcomp::buttons.slot>

                        <div
                            x-show="openOrder == order"
                            x-cloak
                            @click.away="openOrder = false"
                        >
                            <select x-model="option" @change="$wire.setOrderEvent(order, option), option = order">
                                @foreach ($posts->pluck('dyn_order') as $item) 
                                
                                    <option value="{{$item}}" @if($item == $post->dyn_order) @endif >{{$loop->index +1}}</option>
                                @endforeach
                            </select>
                        </div>

                    <div>

                            
                    </x-dashcomp::index.tbl-cell>
                @endif


                <x-dashcomp::index.tbl-cell>
                    {{-- Edit --}}
                    <x-dashcomp::buttons.href-slot
                        href="{{ route('dyndash.edit', ['type' => Str::snake($post->type()), 'id' => $post->id]) }}"
                        :small="true">
                        Edit
                    </x-dashcomp::buttons.slot>


                    
                    {{-- Delete --}}
                    @if (isset($canDelete) && $canDelete)
                        <x-dashcomp::buttons.slot class="!py-1 !px-4 !text-sm" @click="
                            openModal = true;
                            deleteId = {{ $post->id }} " :danger="1">
                            Delete
                        </x-dashcomp::buttons.slot>
                    @endif


                </x-dashcomp::index.tbl-cell>

            </tr>
        @endforeach
    </x-dashcomp::index.table>


    {{-- Modal --}}
    <div x-cloak x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="openModal" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-50 transition-opacity"
                aria-hidden="true"></div>

            <!-- This element is to trick the browser into centering the modal contents. -->
            <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

            {{-- includes are set in the Modal Livewire component and located in the wide/modal/includes folder. --}}

            <x-dashcomp::modal.box @click.outside="openModal = false" @keydown.escape.window="openModal = false">
                <x-dashcomp::modal.content>

                    <x-slot name="body">
                        <div
                            class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <!-- Heroicon name: outline/exclamation -->
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                Delete Model
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Are you sure you want to delete this entry?
                                </p>
                            </div>
                        </div>
                    </x-slot name="body">

                    <x-slot name="footer">
                        <div class="flex items-center space-x-4 py-4">

                            {{-- Delete --}}
                            <form wire:submit.prevent="delete">
                                <div class="flex justify-center py-4">

                                    <button
                                        class="inline-flex justify-center border border-transparent bg-red-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                        type="submit" x-ref="modalDeleteButton" @click="openModal = false">
                                        Delete
                                    </button>
                                </div>
                            </form>

                            {{-- Cancel --}}
                            <button
                                class="focus:ring-none inline-flex justify-center border border-transparent py-2 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none"
                                type="button" @click="openModal = false">
                                Cancel
                            </button>
                        </div>
                    </x-slot name="footer">

                </x-dashcomp::modal.content>
            </x-dashcomp::modal.box>

        </div>
    </div>
</div>
