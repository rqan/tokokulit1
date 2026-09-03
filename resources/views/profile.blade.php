<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - ENY LEATHERÂ®</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind-config.js') }}"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Space Grotesk', sans-serif; }
        .display-font { font-family: 'Space Grotesk', sans-serif; font-weight: 700; letter-spacing: -0.05em; }
    </style>
</head>
<body class="w-full relative antialiased bg-lightBg text-lightMain dark:bg-darkBg dark:text-darkMain min-h-screen flex flex-col">
    <header class="w-full px-8 py-6 flex justify-between items-center border-b border-lightBorder dark:border-darkBorder sticky top-0 z-40 bg-lightBg/90 dark:bg-darkBg/90 backdrop-blur-md">
        <div class="flex-1 flex justify-start">
            <a href="{{ url('/') }}" class="display-font text-4xl tracking-tight">ENY LEATHERÂ®</a>
        </div>
        <div class="flex-1 flex justify-end items-center space-x-6 md:space-x-8 text-[10px] font-semibold tracking-[0.2em] uppercase">
            <a href="{{ url('/katalog?focus_search=1') }}" class="hidden md:block link-hover">Search</a>
            @if(session('isLoggedIn') || Auth::check())
                <a href="{{ url('/pelanggan/cart') }}" class="link-hover">Cart ({{ count(session('cart', [])) }})</a>
                <a href="{{ url('/profile') }}" class="link-hover">Profile</a>
                <button id="themeToggle" class="link-hover uppercase focus:outline-none">Theme</button>
                <a href="{{ url('/logout') }}" class="link-hover text-red-500">Logout</a>
            @else
                <button id="themeToggle" class="link-hover uppercase focus:outline-none">Theme</button>
                <a href="{{ url('/login') }}" class="link-hover">Sign In</a>
            @endif
        </div>
    </header>

    <main class="flex-1 w-full max-w-4xl mx-auto px-8 py-16">
        <h1 class="display-font text-5xl mb-12 uppercase border-b border-lightBorder dark:border-darkBorder pb-6">My Profile</h1>

        <div class="bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder p-8 md:p-12">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                
                <!-- Info Section -->
                <div>
                    <h2 class="text-[10px] tracking-widest uppercase font-bold mb-6 text-lightMuted dark:text-darkMuted">Account Details</h2>
                    
                    <div class="space-y-6">
                        <div>
                            <p class="text-[10px] tracking-widest uppercase text-lightMuted dark:text-darkMuted mb-1">Nama</p>
                            <p class="text-lg font-medium">{{ Auth::user()->name }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] tracking-widest uppercase text-lightMuted dark:text-darkMuted mb-1">Email</p>
                            <p class="text-lg font-medium">{{ Auth::user()->email }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] tracking-widest uppercase text-lightMuted dark:text-darkMuted mb-1">Nomor Telepon</p>
                            <p class="text-lg font-medium">{{ Auth::user()->phone ?? 'Belum diatur' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] tracking-widest uppercase text-lightMuted dark:text-darkMuted mb-1">Role</p>
                            <p class="text-sm font-bold uppercase tracking-widest">{{ Auth::user()->role }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] tracking-widest uppercase text-lightMuted dark:text-darkMuted mb-1">Bergabung Sejak</p>
                            <p class="text-sm font-medium">{{ Auth::user()->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Recent Orders Section -->
        <div class="mt-12 bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder p-8 md:p-12">
            <div class="flex justify-between items-center mb-8 border-b-minimal border-lightBorder dark:border-darkBorder pb-4">
                <h2 class="text-[10px] tracking-widest uppercase font-bold text-lightMuted dark:text-darkMuted">Riwayat Invoice Terakhir</h2>
                <a href="{{ url('/pelanggan/pesanan') }}" class="text-[10px] tracking-widest uppercase font-bold text-lightMain dark:text-darkMain hover:opacity-70 transition-opacity">Lihat Semua</a>
            </div>

            @if(isset($orders) && count($orders) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead>
                            <tr class="border-b border-lightBorder dark:border-darkBorder text-[10px] tracking-widest uppercase text-lightMuted dark:text-darkMuted">
                                <th class="pb-4 font-normal">Invoice</th>
                                <th class="pb-4 font-normal">Tanggal</th>
                                <th class="pb-4 font-normal">Status</th>
                                <th class="pb-4 font-normal">Total</th>
                                <th class="pb-4 font-normal text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr class="border-b border-lightBorder/50 dark:border-darkBorder/50 hover:bg-lightBorder/10 dark:hover:bg-darkBorder/10 transition-colors">
                                    <td class="py-4 font-bold">{{ $order->invoice_number }}</td>
                                    <td class="py-4 text-lightMuted dark:text-darkMuted">{{ $order->created_at->format('d M Y, H:i') }}</td>
                                    <td class="py-4">
                                        <span class="px-3 py-1 text-[9px] tracking-widest uppercase border border-lightBorder dark:border-darkBorder rounded-full">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td class="py-4 font-bold">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    <td class="py-4 text-right">
                                        <a href="{{ url('/pelanggan/pesanan/' . $order->id) }}" class="text-[10px] tracking-widest uppercase font-bold text-lightMain dark:text-darkMain border-b border-lightMain dark:border-darkMain pb-0.5 hover:opacity-70">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-12 text-center text-lightMuted dark:text-darkMuted">
                    <p class="text-sm tracking-widest uppercase">Belum ada riwayat pesanan.</p>
                </div>
            @endif
        </div>
        </div>
    </main>

    <footer class="w-full pt-16 mt-auto">
        <div class="w-full overflow-hidden text-center pb-2 pt-8 border-t border-lightBorder dark:border-darkBorder">
            <h1 class="display-font text-[10vw] leading-none text-lightMain dark:text-darkMain select-none">ENY LEATHERÂ®</h1>
        </div>
                    <div class="w-full flex justify-end px-8 py-4 text-[9px] font-bold tracking-[0.2em] uppercase text-lightMuted dark:text-darkMuted">
            <a href="https://mein-profile.vercel.app" target="_blank" rel="noopener noreferrer" class="hover:text-lightMain dark:hover:text-darkMain transition-colors">&copy; Copyright REGANDEWA 2026</a>
        </div>
</footer>

    <script>
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;
        
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }

        if(themeToggle) {
            themeToggle.addEventListener('click', () => {
                html.classList.toggle('dark');
                localStorage.theme = html.classList.contains('dark') ? 'dark' : 'light';
            });
        }
    </script>
</body>
</html>





