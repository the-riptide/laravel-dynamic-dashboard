<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}" defer></script>

    @stack('head')

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    />

    @livewireStyles
</head>
<body>
    <div class="flex h-screen w-screen">

        <x-dyndash::menu.sidebar :items="$menuItems" />

        <div class="flex-grow h-full relative flex flex-col bg-gray-50">
            <main class="p-md overflow-y-auto">
                <div class="container mx-auto">
                    @yield('body')
                </div>
            </main>
        </div>

    </div>

    @livewireScripts
</body>
</html>
