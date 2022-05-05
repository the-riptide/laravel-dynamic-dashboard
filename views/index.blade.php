{{-- this is a livewire view. For the livewire to work, the whole view must have a wrapping div. --}}
<div
    x-data="{ 
        isOpen: false,
        deleteId : @entangle('deleteId'),
    }" >

    <x-dyndash::buttons.href-slot href="{{ route('dyndash.create', [$type]) }}" class="right-0">
        Create
    </x-dyndash::buttons.href-slot>

    <x-dyndash::index.table>
        <x-slot name="tableHead">
            <tr>
                @foreach ($heads as $head)
                    <x-dyndash::index.thead :title="$head" />
                @endforeach


                <th scope="col" class="relative px-6 py-3">
                    <span class="sr-only">Edit</span>
                </th>
                @if($canDelete)
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Delete</span>
                    </th>
                @endif
            </tr>
        </x-slot>

        @foreach ($posts as $post)
            <tr>
                @foreach ($heads as $head)
                    <x-dyndash::index.tbl-cell class="text-gray-900">
                        {{ $field->setValue($post, $head) }}
                    </x-dyndash::index.tbl-cell>
                @endforeach

                <x-dyndash::index.tbl-cell class="text-right text-sm font-medium text-indigo-600 hover:text-indigo-900">
                    <a href="{{ route('dyndash.edit', ['type' => Str::lower($post->type), 'id' => $post->id]) }} ">Edit</a>
                </x-dyndash::index.tbl-cell>

                @if($canDelete)
                    <td>
                        <x-dyndash::buttons.slot 
                            @click="
                                isOpen = true;
                                deleteId = {{$post->id}}
                            "
                            class="btn-danger btn-small btn-small"
                        >
                            Delete
                        </x-dyndash::buttons.slot>
                    </td>
                @endif

            </tr>
        @endforeach
    </x-dyndash::index.table>

    <div x-cloak 
        x-show="isOpen" 
        class="fixed z-50 inset-0 overflow-y-auto"
        aria-labelledby="modal-title" 
        role="dialog" 
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div 
                x-show="isOpen" 
                x-transition.opacity
                class="fixed inset-0 bg-gray-500 transition-opacity bg-opacity-50"
                aria-hidden="true"></div>

            <!-- This element is to trick the browser into centering the modal contents. -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- includes are set in the Modal Livewire component and located in the wide/modal/includes folder. --}}

            <x-dyndash::modal.box @click.outside="isOpen = false" @keydown.escape.window="isOpen = false">
                <x-dyndash::modal.content>

                    <x-slot name="body">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <!-- Heroicon name: outline/exclamation -->
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
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
                        <div class="py-4 flex justify-center">
                            <button
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:ring-indigo-500"
                                type="button" @click="isOpen = false">
                                Cancel
                            </button>
                        </div>
                        <form wire:submit.prevent="delete">
                            <div class="py-4 flex justify-center">

                                <button
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 bg-red-600 text-white hover:bg-red-700 focus:ring-red-500"
                                    type="submit" x-ref="modalDeleteButton" @click="isOpen = false">
                                    Delete
                                </button>
                            </div>
                        </form>
                    </x-slot name="footer">

                </x-dyndash::modal.content>
            </x-dyndash::modal.box>

        </div>
    </div>
</div>
