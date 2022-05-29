@props(['active'])

@php
    $classes = 'block pl-3 pr-4 py-2 text-base font-medium transition border-l-4 focus:outline-none'.
        ($active ?? false)
            ? '  border-indigo-400 text-indigo-700 bg-indigo-50  focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700 '
            : ' border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
