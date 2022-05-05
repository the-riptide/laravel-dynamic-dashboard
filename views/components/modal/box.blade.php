<div
    x-transition.opacity.duration.300ms
    {{ $attributes->merge([
        'class' => '
            inline-block align-bottom 
            bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full
            ',
        'x-show' => 'isOpen',
        ])
    }}
>
    {{-- Gradient Strip --}}
    <div class="gradient-strip"></div>
    
    {{-- Content --}}
    {{$slot}}

</div>
