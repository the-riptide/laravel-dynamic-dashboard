@props(['items'])


<div
    class="w-80 bg-white h-screen p-xl drop-shadow-md border-r border-gray-200 overflow-auto flex-none py-10"
>
    
    <ul>
        @foreach($items as $key => $item)
            <!-- Main Menu Items -->
            <div class="py-1 flex justify-between border-t border-gray-200 content-center">
                <li class=
                    "relative
                    hover:text-gray-800
                    transition-colors duration-150 mb-0 py-sm"
                >
                <a href="{{route('dyndash.index', [$item])}}" class="inline-flex items-center rounded w-full">
            
                    
                    <span class="ml-4">{{ $key }}</span>
                    
                </a>
            
            </div>

        @endforeach
    </ul>
</div>
