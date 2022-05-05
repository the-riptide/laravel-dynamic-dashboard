<button
    {{$attributes->merge(['class' => 
        'rounded
        bg-indigo-600 text-white
        hover:bg-gradient-to-r hover:from-indigo-600 hover:to-red-600 bg-no-repeat
        transition-all
        py-2 px-5 
        shadow-sm text-base font-bold
        focus:ring-indigo-500 focus:ring-2 ring-offset-2
        inline-block
        text-center tracking-wider', 
    'type' => 'button'])}}
>

    {{$slot}}

</button>
