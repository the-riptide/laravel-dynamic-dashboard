@props(['active' => false])

<a
    {{ $attributes->class([
            'inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium leading-5 transition focus:border-indigo-700 focus:outline-none',
            'border-indigo-500 text-gray-900' => $active,
            'text-gray-500 hover:text-gray-700' => !$active,
        ])->merge() }}>
    {{ $slot }}
</a>
