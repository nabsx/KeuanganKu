<!DOCTYPE html>
<html lang="id" class="bg-background">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 - Terlalu Banyak Permintaan</title>
    <link rel="icon" type="image/png" href="{{ asset('images/keuanganku-logo.png') }}">
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
                },
            },
        };
    </script>
</head>
<body class="bg-background text-text-primary">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-lg w-full text-center">
            <!-- Logo -->
            <div class="mb-8 flex justify-center">
                <img src="{{ asset('images/keuanganku-logo.png') }}" alt="KeuanganKu Logo" class="h-20 w-20 object-contain">
            </div>

            <!-- 429 Number -->
            <div class="mb-6">
                <h1 class="text-9xl font-bold text-info mb-2">429</h1>
                <div class="h-1 w-24 bg-info mx-auto mb-4"></div>
            </div>

            <!-- Error Message -->
            <div class="mb-8">
                <h2 class="text-3xl font-bold mb-3">Terlalu Banyak Permintaan</h2>
                <p class="text-text-secondary text-lg">
                    Anda telah mengirimkan terlalu banyak permintaan dalam waktu singkat. Silakan tunggu beberapa saat sebelum mencoba lagi.
                </p>
            </div>

            <!-- Illustration -->
            <div class="mb-8 p-8 bg-surface-secondary rounded-2xl border border-border-light">
                <svg class="w-24 h-24 mx-auto text-info opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="javascript:history.back()" class="px-6 py-3 bg-surface-secondary border border-border-light text-text-primary font-semibold rounded-lg hover:bg-surface transition">
                    Kembali
                </a>
                <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-info text-white font-semibold rounded-lg hover:bg-info-dark transition">
                    Ke Dashboard
                </a>
            </div>

            <!-- Footer Text -->
            <p class="mt-8 text-text-tertiary text-sm">
                KeuanganKu - Kelola Keuanganmu Lebih Cerdas
            </p>
        </div>
    </div>
</body>
</html>
