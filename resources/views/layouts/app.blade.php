<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Catat Pendapatan & Wallet')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8',
                        },
                    },
                },
            },
        };
    </script>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">

@auth
<nav class="bg-white border-b shadow-sm sticky top-0 z-10">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <a href="{{ route('dashboard') }}" class="font-bold text-primary-700 text-lg">💰 KeuanganKu</a>

            <button id="menuBtn" class="md:hidden p-2 text-gray-600" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100' }}">Dashboard</a>
                <a href="{{ route('wallets.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('wallets.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100' }}">Wallet</a>
                <a href="{{ route('incomes.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('incomes.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100' }}">Pendapatan</a>
                <a href="{{ route('allocations.edit') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('allocations.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100' }}">Persentase</a>
                <a href="{{ route('telegram.edit') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('telegram.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100' }}">Telegram</a>
                <span class="mx-2 text-gray-300">|</span>
                <span class="text-sm text-gray-500">Hai, {{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="px-3 py-2 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50">Keluar</button>
                </form>
            </div>
        </div>

        <div id="mobileMenu" class="hidden md:hidden pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">Dashboard</a>
            <a href="{{ route('wallets.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">Wallet</a>
            <a href="{{ route('incomes.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">Pendapatan</a>
            <a href="{{ route('allocations.edit') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">Persentase</a>
            <a href="{{ route('telegram.edit') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">Telegram</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50">Keluar ({{ auth()->user()->name }})</button>
            </form>
        </div>
    </div>
</nav>
@endauth

<main class="flex-1 max-w-6xl w-full mx-auto px-4 py-6">
    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
            <p class="font-medium mb-1">Terjadi kesalahan:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

<footer class="text-center text-xs text-gray-400 py-4">
    &copy; {{ date('Y') }} KeuanganKu — Catat Pendapatan &amp; Manajemen Wallet
</footer>

<script>
    document.getElementById('menuBtn')?.addEventListener('click', function () {
        document.getElementById('mobileMenu').classList.toggle('hidden');
    });
</script>
@stack('scripts')
</body>
</html>
