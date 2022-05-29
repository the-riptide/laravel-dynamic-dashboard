<div class="overflow-hidden border border-gray-200 text-sm shadow-sm sm:rounded-lg" {{ $attributes }}>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            {{ $tableHead }}
        </thead>

        <tbody class="divide-y divide-gray-100 bg-white">
            {{ $slot }}
        </tbody>
    </table>
</div>
