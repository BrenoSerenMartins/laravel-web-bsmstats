<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'BSMStats')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-900 text-white">
    <div class="container mx-auto px-4 py-8">
        <header class="flex justify-between items-center mb-8">
            <a href="/" class="text-2xl font-bold text-white">EzStats</a>
            <nav>
                <a href="/leaderboard" class="text-gray-400 hover:text-white mr-4">Leaderboards</a>
                <a href="/champions" class="text-gray-400 hover:text-white mr-4">Champions</a>
                <a href="/items" class="text-gray-400 hover:text-white mr-4">Items</a>
                <a href="/summoner-spells" class="text-gray-400 hover:text-white mr-4">Summoner Spells</a>
                <a href="/profile-icons" class="text-gray-400 hover:text-white">Profile Icons</a>
            </nav>
        </header>

        <main>
            @yield('content')
        </main>
    </div>

    @stack('scripts')

</body>

</html>