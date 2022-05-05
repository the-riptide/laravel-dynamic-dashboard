<div 
    class="text-sm shadow border border-gray-200 sm:rounded-lg overflow-hidden"
    {{$attributes}}    
>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            {{$tableHead}}
        </thead>

        <tbody class="bg-white divide-y divide-gray-100">
            {{$slot}}
        </tbody>
    </table>
</div>
