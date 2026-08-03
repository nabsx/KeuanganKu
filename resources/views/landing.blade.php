<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KeuanganKu - Kelola Keuanganmu Lebih Cerdas</title>
    <link rel="icon" type="image/png" href="{{ asset('images/keuanganku-logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        /* Gradient backgrounds */
        .gradient-primary {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        }
        
        .gradient-dark-accent {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(34, 197, 94, 0.05) 100%);
        }
        
        /* Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .glass-hover:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(16, 185, 129, 0.3);
            transform: translateY(-4px);
        }
        
        /* Glow effect */
        .glow-green {
            box-shadow: 0 0 30px rgba(16, 185, 129, 0.4);
        }
        
        .glow-hover:hover {
            box-shadow: 0 0 40px rgba(16, 185, 129, 0.6);
        }
        
        /* Smooth animations */
        .fade-in {
            animation: fadeIn 0.8s ease-in;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-15px);
            }
        }
        
        /* Text gradient */
        .text-gradient {
            background: linear-gradient(135deg, #10B981 0%, #22C55E 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #0a0a0a;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #10B981;
            border-radius: 4px;
        }
    </style>
</head>
<body class="bg-[#050505] text-white overflow-x-hidden">

<!-- ========== NAVBAR ========== -->
<nav class="fixed w-full top-0 z-50 glass border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center gap-2">
                <img src="{{ asset('images/keuanganku-logo.png') }}" alt="KeuanganKu Logo" class="w-8 h-8 object-contain">
                <span class="text-lg font-bold">KeuanganKu</span>
            </div>

            <!-- Menu Desktop -->
            <div class="hidden md:flex items-center gap-8">
                <a href="#home" class="text-sm hover:text-green-400 transition">Home</a>
                <a href="#features" class="text-sm hover:text-green-400 transition">Fitur</a>
                <a href="#how-it-works" class="text-sm hover:text-green-400 transition">Cara Kerja</a>
                <a href="#dashboard" class="text-sm hover:text-green-400 transition">Dashboard</a>
            </div>

            <!-- Auth Buttons -->
            <div class="flex items-center gap-3">
                @guest
                <a href="{{ route('login') }}" class="text-sm px-4 py-2 rounded-lg hover:bg-white/5 transition">Login</a>
                <a href="{{ route('register') }}" class="text-sm px-4 py-2 rounded-lg gradient-primary text-white font-medium hover:opacity-90 transition">Daftar</a>
                @else
                <a href="{{ route('dashboard') }}" class="text-sm px-4 py-2 rounded-lg gradient-primary text-white font-medium hover:opacity-90 transition">Dashboard</a>
                @endguest
            </div>
        </div>
    </div>
</nav>

<!-- ========== HERO SECTION ========== -->
<section id="home" class="pt-32 pb-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <div class="absolute inset-0 -z-10 overflow-hidden">
        <div class="absolute top-0 left-1/4 w-96 h-96 rounded-full bg-green-900/20 blur-3xl"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 rounded-full bg-green-900/10 blur-3xl"></div>
    </div>

    <div class="max-w-7xl mx-auto">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <!-- Left Content -->
            <div class="fade-in">
                <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                    <span class="text-gradient">Kelola Keuanganmu Lebih Cerdas</span>
                </h1>
                <p class="text-xl text-gray-400 mb-8 leading-relaxed">
                    Catat pendapatan, atur wallet, dan bagi uang secara otomatis sesuai kebutuhanmu. Satu aplikasi untuk mengontrol semua keuangan Anda.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('register') }}" class="px-8 py-4 gradient-primary text-white font-semibold rounded-xl hover:opacity-90 transition text-center glow-green">
                        Mulai Sekarang
                    </a>
                    @auth
                    <a href="{{ route('dashboard') }}" class="px-8 py-4 glass text-white font-semibold rounded-xl hover:bg-white/10 transition text-center">
                        Buka Dashboard
                    </a>
                    @else
                    <a href="{{ route('login') }}" class="px-8 py-4 glass text-white font-semibold rounded-xl hover:bg-white/10 transition text-center">
                        Login
                    </a>
                    @endauth
                </div>
            </div>

            <!-- Right Illustration -->
            <div class="hidden md:block fade-in float-animation">
                <div class="glass rounded-2xl p-8 glow-green">
                    <div class="space-y-4">
                        <!-- Dashboard Card -->
                        <div class="bg-white/5 rounded-xl p-6 border border-green-500/20">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <p class="text-sm text-gray-400">Total Saldo</p>
                                    <h3 class="text-2xl font-bold text-green-400">Rp 25.000.000</h3>
                                </div>
                                <div class="w-10 h-10 rounded-full gradient-primary"></div>
                            </div>
                            <div class="flex gap-2">
                                <div class="flex-1 h-2 bg-green-500 rounded-full"></div>
                                <div class="flex-1 h-2 bg-yellow-500/30 rounded-full"></div>
                                <div class="flex-1 h-2 bg-blue-500/30 rounded-full"></div>
                            </div>
                        </div>

                        <!-- Wallet Cards -->
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                                <p class="text-xs text-gray-400">Tabungan</p>
                                <p class="text-lg font-semibold text-green-400">Rp 10M</p>
                            </div>
                            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                                <p class="text-xs text-gray-400">Investasi</p>
                                <p class="text-lg font-semibold text-blue-400">Rp 8M</p>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="bg-gradient-to-r from-green-500/10 to-blue-500/10 rounded-lg p-4 border border-green-500/20">
                            <p class="text-xs text-gray-400 mb-1">Pendapatan Bulan Ini</p>
                            <p class="text-xl font-bold text-green-400">Rp 15.000.000 ↗</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== FEATURES SECTION ========== -->
<section id="features" class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-dark-accent">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16 fade-in">
            <h2 class="text-4xl md:text-5xl font-bold mb-4">Semua Keuanganmu Dalam Satu Tempat</h2>
            <p class="text-xl text-gray-400">Fitur lengkap untuk mengelola keuangan Anda dengan mudah dan aman</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="glass glass-hover rounded-2xl p-8 transition-all duration-300">
                <div class="w-12 h-12 rounded-xl gradient-primary flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Smart Wallet Management</h3>
                <p class="text-gray-400">Tambah dan kelola banyak wallet sesuai kebutuhan. Organize keuangan Anda dengan lebih terstruktur.</p>
            </div>

            <!-- Feature 2 -->
            <div class="glass glass-hover rounded-2xl p-8 transition-all duration-300">
                <div class="w-12 h-12 rounded-xl gradient-primary flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Automatic Money Allocation</h3>
                <p class="text-gray-400">Bagikan pendapatan otomatis berdasarkan persentase yang sudah Anda atur.</p>
            </div>

            <!-- Feature 3 -->
            <div class="glass glass-hover rounded-2xl p-8 transition-all duration-300">
                <div class="w-12 h-12 rounded-xl gradient-primary flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Expense Tracking</h3>
                <p class="text-gray-400">Catat semua transaksi dan pantau pengeluaran dengan detail yang akurat.</p>
            </div>

            <!-- Feature 4 -->
            <div class="glass glass-hover rounded-2xl p-8 transition-all duration-300">
                <div class="w-12 h-12 rounded-xl gradient-primary flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11a4 4 0 118 0 4 4 0 01-8 0zM9 19h6"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Telegram Notification</h3>
                <p class="text-gray-400">Dapatkan notifikasi transaksi secara real-time langsung ke Telegram Anda.</p>
            </div>

            <!-- Feature 5 -->
            <div class="glass glass-hover rounded-2xl p-8 transition-all duration-300">
                <div class="w-12 h-12 rounded-xl gradient-primary flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Secure Account</h3>
                <p class="text-gray-400">Data keuangan Anda aman dengan enkripsi tingkat enterprise.</p>
            </div>

            <!-- Feature 6 -->
            <div class="glass glass-hover rounded-2xl p-8 transition-all duration-300">
                <div class="w-12 h-12 rounded-xl gradient-primary flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12a3 3 0 11-6 0 3 3 0 016 0zM14 12a3 3 0 11-6 0 3 3 0 016 0zM20.5 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Financial Report</h3>
                <p class="text-gray-400">Lihat perkembangan keuangan dengan visualisasi data yang mudah dipahami.</p>
            </div>
        </div>
    </div>
</section>

<!-- ========== HOW IT WORKS SECTION ========== -->
<section id="how-it-works" class="py-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16 fade-in">
            <h2 class="text-4xl md:text-5xl font-bold mb-4">Cara Kerjanya</h2>
            <p class="text-xl text-gray-400">Ikuti 5 langkah sederhana untuk mulai mengelola keuangan Anda</p>
        </div>

        <div class="space-y-8">
            <!-- Step 1 -->
            <div class="flex gap-8 items-start fade-in">
                <div class="flex-shrink-0 w-12 h-12 rounded-full gradient-primary flex items-center justify-center font-bold text-lg">1</div>
                <div>
                    <h3 class="text-2xl font-bold mb-2">Daftar Akun</h3>
                    <p class="text-gray-400">Buat akun KeuanganKu Anda dengan email dan password yang aman.</p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="flex gap-8 items-start fade-in">
                <div class="flex-shrink-0 w-12 h-12 rounded-full gradient-primary flex items-center justify-center font-bold text-lg">2</div>
                <div>
                    <h3 class="text-2xl font-bold mb-2">Buat Wallet</h3>
                    <p class="text-gray-400">Tambahkan wallet untuk berbagai kebutuhan, seperti tabungan, investasi, cicilan, dll.</p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="flex gap-8 items-start fade-in">
                <div class="flex-shrink-0 w-12 h-12 rounded-full gradient-primary flex items-center justify-center font-bold text-lg">3</div>
                <div>
                    <h3 class="text-2xl font-bold mb-2">Atur Persentase</h3>
                    <p class="text-gray-400">Tentukan berapa persen pendapatan Anda yang akan masuk ke setiap wallet.</p>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="flex gap-8 items-start fade-in">
                <div class="flex-shrink-0 w-12 h-12 rounded-full gradient-primary flex items-center justify-center font-bold text-lg">4</div>
                <div>
                    <h3 class="text-2xl font-bold mb-2">Masukkan Pendapatan</h3>
                    <p class="text-gray-400">Catat setiap pendapatan yang Anda terima ke dalam aplikasi.</p>
                </div>
            </div>

            <!-- Step 5 -->
            <div class="flex gap-8 items-start fade-in">
                <div class="flex-shrink-0 w-12 h-12 rounded-full gradient-primary flex items-center justify-center font-bold text-lg">5</div>
                <div>
                    <h3 class="text-2xl font-bold mb-2">Uang Otomatis Terbagi</h3>
                    <p class="text-gray-400">Sistem akan otomatis membagi uang ke setiap wallet berdasarkan persentase yang Anda atur!</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== DASHBOARD PREVIEW SECTION ========== -->
<section id="dashboard" class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-dark-accent">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16 fade-in">
            <h2 class="text-4xl md:text-5xl font-bold mb-4">Preview Dashboard</h2>
            <p class="text-xl text-gray-400">Desain dashboard yang intuitif dan mudah digunakan</p>
        </div>

        <div class="glass rounded-3xl p-8 glow-green overflow-hidden">
            <div class="space-y-6">
                <!-- Header -->
                <div class="flex justify-between items-start pb-6 border-b border-white/10">
                    <div>
                        <h3 class="text-2xl font-bold mb-2">Dashboard Keuangan</h3>
                        <p class="text-gray-400">Ringkasan keuangan Anda hari ini</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-400">Total Saldo</p>
                        <p class="text-3xl font-bold text-gradient">Rp 45.000.000</p>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="bg-white/5 rounded-xl p-6 border border-white/10">
                        <p class="text-sm text-gray-400 mb-2">Pendapatan Bulan Ini</p>
                        <p class="text-2xl font-bold text-green-400">Rp 30.000.000</p>
                        <p class="text-xs text-green-400 mt-2">↗ 12% dari bulan lalu</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-6 border border-white/10">
                        <p class="text-sm text-gray-400 mb-2">Total Pengeluaran</p>
                        <p class="text-2xl font-bold text-red-400">Rp 5.000.000</p>
                        <p class="text-xs text-red-400 mt-2">↘ 8% dari bulan lalu</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-6 border border-white/10">
                        <p class="text-sm text-gray-400 mb-2">Transaksi Hari Ini</p>
                        <p class="text-2xl font-bold text-blue-400">5</p>
                        <p class="text-xs text-blue-400 mt-2">2 masuk, 3 keluar</p>
                    </div>
                </div>

                <!-- Wallets -->
                <div>
                    <h4 class="text-lg font-bold mb-4">Wallet Anda</h4>
                    <div class="grid md:grid-cols-4 gap-4">
                        <div class="bg-gradient-to-br from-green-500/20 to-green-600/20 rounded-xl p-4 border border-green-500/30">
                            <p class="text-xs text-gray-400 mb-2">Tabungan</p>
                            <p class="text-xl font-bold">Rp 20M</p>
                            <div class="mt-3 h-2 bg-white/10 rounded-full overflow-hidden">
                                <div class="h-full w-4/5 gradient-primary"></div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-xl p-4 border border-blue-500/30">
                            <p class="text-xs text-gray-400 mb-2">Investasi</p>
                            <p class="text-xl font-bold">Rp 15M</p>
                            <div class="mt-3 h-2 bg-white/10 rounded-full overflow-hidden">
                                <div class="h-full w-3/5 bg-blue-500"></div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-yellow-500/20 to-yellow-600/20 rounded-xl p-4 border border-yellow-500/30">
                            <p class="text-xs text-gray-400 mb-2">Cicilan</p>
                            <p class="text-xl font-bold">Rp 5M</p>
                            <div class="mt-3 h-2 bg-white/10 rounded-full overflow-hidden">
                                <div class="h-full w-1/3 bg-yellow-500"></div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-purple-500/20 to-purple-600/20 rounded-xl p-4 border border-purple-500/30">
                            <p class="text-xs text-gray-400 mb-2">Darurat</p>
                            <p class="text-xl font-bold">Rp 5M</p>
                            <div class="mt-3 h-2 bg-white/10 rounded-full overflow-hidden">
                                <div class="h-full w-1/4 bg-purple-500"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div>
                    <h4 class="text-lg font-bold mb-4">Transaksi Terakhir</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-white/5 rounded-lg border border-white/10">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center">+</div>
                                <div>
                                    <p class="font-medium">Pendapatan Freelance</p>
                                    <p class="text-xs text-gray-400">Hari ini, 10:30</p>
                                </div>
                            </div>
                            <p class="font-bold text-green-400">+ Rp 2.000.000</p>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-white/5 rounded-lg border border-white/10">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-red-500/20 flex items-center justify-center">-</div>
                                <div>
                                    <p class="font-medium">Pembayaran Cicilan Mobil</p>
                                    <p class="text-xs text-gray-400">Kemarin, 14:15</p>
                                </div>
                            </div>
                            <p class="font-bold text-red-400">- Rp 1.500.000</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== CTA SECTION ========== -->
<section class="py-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <div class="glass rounded-3xl p-12 text-center glow-green gradient-dark-accent">
            <h2 class="text-4xl md:text-5xl font-bold mb-6">Mulai Atur Keuanganmu Hari Ini</h2>
            <p class="text-xl text-gray-400 mb-8">
                Satu aplikasi untuk mengontrol pendapatan, tabungan, dan tujuan finansialmu. Bergabunglah dengan ribuan pengguna KeuanganKu.
            </p>
            <a href="{{ route('register') }}" class="inline-block px-10 py-4 gradient-primary text-white font-semibold rounded-xl hover:opacity-90 transition">
                Daftar Gratis Sekarang
            </a>
        </div>
    </div>
</section>

<!-- ========== FOOTER ========== -->
<footer class="border-t border-white/10 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="grid md:grid-cols-4 gap-8 mb-8">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg gradient-primary flex items-center justify-center text-white font-bold">💰</div>
                    <span class="font-bold">KeuanganKu</span>
                </div>
                <p class="text-gray-400 text-sm">Kelola keuanganmu lebih cerdas dengan teknologi terdepan.</p>
            </div>
            <div>
                <h4 class="font-bold mb-4">Produk</h4>
                <ul class="space-y-2 text-gray-400 text-sm">
                    <li><a href="#features" class="hover:text-green-400 transition">Fitur</a></li>
                    <li><a href="#how-it-works" class="hover:text-green-400 transition">Cara Kerja</a></li>
                    <li><a href="#dashboard" class="hover:text-green-400 transition">Dashboard</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4">Akun</h4>
                <ul class="space-y-2 text-gray-400 text-sm">
                    <li><a href="{{ route('login') }}" class="hover:text-green-400 transition">Login</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-green-400 transition">Daftar</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4">Legal</h4>
                <ul class="space-y-2 text-gray-400 text-sm">
                    <li><a href="#" class="hover:text-green-400 transition">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-green-400 transition">Terms of Service</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-gray-400 text-sm">
            <p>&copy; {{ date('Y') }} KeuanganKu. Semua hak dilindungi.</p>
            <p>Powered By Nabsx</p>
        </div>
    </div>
</footer>

<script>
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // Add fade-in animation on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('section').forEach(section => {
        observer.observe(section);
    });
</script>

</body>
</html>
