<!DOCTYPE html>
<html lang="id" class="bg-background">
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
                        background: '#000000',
                        surface: '#0a0a0a',
                        'surface-secondary': '#111111',
                        border: '#1a1a1a',
                        'border-light': '#262626',
                        'text-primary': '#ffffff',
                        'text-secondary': '#a0a0a0',
                        'text-tertiary': '#737373',
                        accent: '#10b981',
                        'accent-dark': '#059669',
                        'accent-light': '#34d399',
                        success: '#10b981',
                        error: '#ef4444',
                        warning: '#f59e0b',
                        info: '#3b82f6',
                    },
                    boxShadow: {
                        glass: '0 8px 32px rgba(0, 0, 0, 0.4)',
                    },
                    backgroundColor: {
                        glass: 'rgba(255, 255, 255, 0.05)',
                        'glass-light': 'rgba(255, 255, 255, 0.08)',
                    },
                },
            },
        };
    </script>
</head>
<body class="bg-background text-text-primary min-h-screen flex flex-col md:flex-row">

@auth
<!-- Desktop Sidebar -->
<aside class="hidden md:flex md:w-64 md:flex-col md:fixed md:left-0 md:top-0 md:h-screen md:bg-surface-secondary md:border-r md:border-border-light md:z-20">
    <div class="p-6 border-b border-border-light flex items-center justify-center">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center">
            <img src="{{ asset('images/keuanganku-logo.png') }}" alt="KeuanganKu Logo" class="h-12 w-12 object-contain">
        </a>
    </div>

    <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-glass-light text-accent border border-accent border-opacity-20' : 'text-text-secondary hover:bg-glass hover:text-text-primary' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 16l9-8-9-8"/>
            </svg>
            Dashboard
        </a>
        <a href="{{ route('wallets.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('wallets.*') ? 'bg-glass-light text-accent border border-accent border-opacity-20' : 'text-text-secondary hover:bg-glass hover:text-text-primary' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Wallet
        </a>
        <a href="{{ route('incomes.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('incomes.*') ? 'bg-glass-light text-accent border border-accent border-opacity-20' : 'text-text-secondary hover:bg-glass hover:text-text-primary' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Pendapatan
        </a>
        <a href="{{ route('allocations.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('allocations.*') ? 'bg-glass-light text-accent border border-accent border-opacity-20' : 'text-text-secondary hover:bg-glass hover:text-text-primary' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Persentase
        </a>
        <a href="{{ route('data-backup.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('data-backup.*') ? 'bg-glass-light text-accent border border-accent border-opacity-20' : 'text-text-secondary hover:bg-glass hover:text-text-primary' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Zm2 4h6m-6 4h6m-6 4h3"/></svg>
            Data & Backup
        </a>
        <a href="{{ route('telegram.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('telegram.*') ? 'bg-glass-light text-accent border border-accent border-opacity-20' : 'text-text-secondary hover:bg-glass hover:text-text-primary' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Telegram
        </a>
    </nav>

    <div class="p-4 border-t border-border-light">
        <div class="mb-4 px-4 py-3 bg-glass rounded-lg border border-border-light">
            <p class="text-xs text-text-tertiary mb-1">Masuk sebagai</p>
            <p class="text-sm font-medium text-text-primary">{{ auth()->user()->name }}</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-error hover:bg-glass transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Keluar
            </button>
        </form>
    </div>
</aside>

<!-- Mobile Navbar + Main Content Wrapper -->
<div class="flex-1 md:ml-64 w-full flex flex-col">
    <!-- Mobile Header -->
    <header class="md:hidden bg-surface-secondary border-b border-border-light sticky top-0 z-10">
        <div class="flex justify-between items-center h-16 px-4">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center">
                <img src="{{ asset('images/keuanganku-logo.png') }}" alt="KeuanganKu Logo" class="h-10 w-10 object-contain">
            </a>
            <button id="mobileMenuBtn" class="p-2 text-text-secondary hover:text-text-primary transition" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        <!-- Mobile Menu Drawer -->
        <nav id="mobileMenu" class="hidden px-4 pb-4 space-y-1 bg-surface border-t border-border-light">
            <a href="{{ route('dashboard') }}" class="block px-4 py-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-glass-light text-accent' : 'text-text-secondary hover:bg-glass' }}">Dashboard</a>
            <a href="{{ route('wallets.index') }}" class="block px-4 py-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('wallets.*') ? 'bg-glass-light text-accent' : 'text-text-secondary hover:bg-glass' }}">Wallet</a>
            <a href="{{ route('incomes.index') }}" class="block px-4 py-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('incomes.*') ? 'bg-glass-light text-accent' : 'text-text-secondary hover:bg-glass' }}">Pendapatan</a>
            <a href="{{ route('allocations.edit') }}" class="block px-4 py-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('allocations.*') ? 'bg-glass-light text-accent' : 'text-text-secondary hover:bg-glass' }}">Persentase</a>
            <a href="{{ route('data-backup.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('data-backup.*') ? 'bg-glass-light text-accent' : 'text-text-secondary hover:bg-glass' }}">Data & Backup</a>
            <a href="{{ route('telegram.edit') }}" class="block px-4 py-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('telegram.*') ? 'bg-glass-light text-accent' : 'text-text-secondary hover:bg-glass' }}">Telegram</a>
            <div class="my-4 border-t border-border-light pt-4">
                <p class="text-xs text-text-tertiary px-4 mb-2">Masuk sebagai</p>
                <p class="text-sm font-medium text-text-primary px-4 mb-3">{{ auth()->user()->name }}</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full text-left px-4 py-3 rounded-lg text-sm font-medium text-error hover:bg-glass transition">Keluar</button>
                </form>
            </div>
        </nav>
    </header>
@endauth

    <!-- Main Content Area -->
    <main class="flex-1 px-4 md:px-8 py-6 md:py-8 overflow-y-auto">
        <!-- Alert Messages -->
        @if (session('success'))
            <div class="mb-4 rounded-lg bg-glass-light border border-accent border-opacity-20 text-success px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-lg bg-glass-light border border-error border-opacity-20 text-error px-4 py-3 text-sm">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-glass-light border border-error border-opacity-20 text-error px-4 py-3 text-sm">
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

@auth
</div>
@endauth



<script>
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function () {
            mobileMenu.classList.toggle('hidden');
        });
    }
</script>
@stack('scripts')
</body>
</html>
