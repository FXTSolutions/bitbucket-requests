<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laravel Application')</title>
    @vite('resources/css/app.css')
</head>
<body class="flex flex-col min-h-screen bg-gray-100 text-gray-800">
    <header class="bg-blue-600 text-white p-4">
        <h1 class="text-center">@yield('header', 'Laravel Application')</h1>
    </header>

    <main class="flex-grow p-4">
        @yield('content')
    </main>

<footer class="bg-blue-600 text-white text-center p-2">
    <p>&copy; {{ date('Y') }} Bitbucket Pull Request Application</p>
</footer>
</body>
</html>
