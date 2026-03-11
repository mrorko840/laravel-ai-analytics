<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Analytics</title>
    <!-- Use a simple CDN tailwind for package simplicity without requiring build steps -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 text-gray-900 font-sans antialiased">
    <div class="min-h-screen flex flex-col">
        <header class="bg-white border-b sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <h1
                                class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                                AI Analytics
                            </h1>
                        </div>
                        <nav class="ml-6 flex items-center space-x-4">
                            <a href="{{ route('ai-analytics.dashboard') }}"
                                class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                            <a href="{{ route('ai-analytics.cards.index') }}"
                                class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Cards</a>
                            <a href="{{ route('ai-analytics.chat') }}"
                                class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Chat Assistant</a>
                            <a href="{{ route('ai-analytics.reports') }}"
                                class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Reports</a>
                            <a href="{{ route('ai-analytics.data-sources') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium border-l pl-4 ml-2">Data Sources</a>
                        </nav>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-grow w-full max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            @yield('content')
        </main>
    </div>
</body>

</html>