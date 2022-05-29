@props(['danger' => false , 'small' => false])
<button
    {{ $attributes->class([
            'rounded bg-indigo-600 hover:bg-indigo-800 text-white py-2 px-6 shadow-sm text-base font-bold focus:ring-indigo-500 focus:ring-2 ring-offset-2 inline-block text-center tracking-wider',
            'hover:!bg-red-600 !bg-red-500 focus:ring-red-500 ' => $danger,
            '!py-1 px-4 !text-sm' => $small,
        ])->merge(['type' => 'button']) }}>

    {{ $slot }}

</button>
