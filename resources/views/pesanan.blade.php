<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind-config.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ux.css') }}">
    <script src="{{ asset('js/ux.js') }}"></script>
</head>
<body class="w-full relative antialiased selection:bg-lightMain selection:text-lightBg dark:selection:bg-darkMain dark:selection:text-darkBg bg-lightBg text-lightMain dark:bg-darkBg dark:text-darkMain">

    

    <header class="w-full px-8 py-6 flex justify-between items-center border-b-minimal border-lightBorder dark:border-darkBorder sticky top-0 z-40 bg-lightBg dark:bg-darkBg transition-colors">
        <div class="flex-1 flex justify-start">
            <a href="{{ url('/') }}" class="display-font text-4xl tracking-tight">ENY LEATHER</a>
        </div>
        <div class="hidden md:flex flex-1 justify-center space-x-12 text-[10px] font-semibold tracking-[0.2em] uppercase">
            @if(strtolower(request('gender')) == 'men')
                <a href="{{ url('/katalog?' . http_build_query(request()->except('gender'))) }}" class="link-hover text-red-500 hover:text-red-600">Back</a>
            @else
                <a href="{{ url('/katalog?gender=Men' . (request('q') ? '&q='.request('q') : '') . (request('category') ? '&category='.request('category') : '')) }}" class="link-hover">Men</a>
            @endif
            @if(strtolower(request('gender')) == 'women')
                <a href="{{ url('/katalog?' . http_build_query(request()->except('gender'))) }}" class="link-hover text-red-500 hover:text-red-600">Back</a>
            @else
                <a href="{{ url('/katalog?gender=Women' . (request('q') ? '&q='.request('q') : '') . (request('category') ? '&category='.request('category') : '')) }}" class="link-hover">Women</a>
            @endif
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

    <main class="w-full min-h-[70vh] px-8 py-16 max-w-5xl mx-auto">
        <h1 class="display-font text-5xl md:text-6xl mb-12 uppercase border-b-minimal border-lightBorder dark:border-darkBorder pb-8">Riwayat Pesanan</h1>

        @if(session('success'))
            <div class="mb-8 p-4 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 text-xs tracking-widest uppercase text-center border border-green-200 dark:border-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if(isset($orders) && $orders->count() > 0)
            <div class="space-y-6">
                @foreach($orders as $order)
                    <div class="border border-lightBorder dark:border-darkBorder p-6 hover:border-lightMain dark:hover:border-darkMain transition-colors flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        
                        <div class="flex-1 space-y-3">
                            <div class="flex items-center gap-4">
                                <h3 class="display-font text-xl uppercase tracking-wider">
                                    @if($order->invoice_number){{ $order->invoice_number }}@else<span class="text-red-500">BELUM ADA INVOICE</span>@endif
                                </h3>
                                <span class="px-3 py-1 text-[9px] font-bold tracking-widest uppercase border border-{{ $order->status_color ?? 'gray' }}-500 text-{{ $order->status_color ?? 'gray' }}-600 dark:text-{{ $order->status_color ?? 'gray' }}-400">
                                    {{ $order->status_label ?? $order->status }}
                                </span>
                            </div>
                            
                            <div class="text-[10px] tracking-widest uppercase text-lightMuted dark:text-darkMuted flex gap-4">
                                <span>{{ $order->created_at->format('d M Y, H:i') }}</span>
                                <span>•</span>
                                <span>{{ $order->items->count() ?? 0 }} Produk</span>
                            </div>
                        </div>

                        <div class="flex flex-col items-start md:items-end gap-3 w-full md:w-auto">
                            <div class="text-sm font-bold tracking-widest uppercase">
                                Rp{{ number_format($order->grand_total, 0, ',', '.') }}
                            </div>
                            <a href="{{ url('/pelanggan/pesanan/' . $order->id) }}" class="inline-block border-b border-lightMain dark:border-darkMain pb-1 text-[10px] tracking-[0.2em] uppercase font-bold hover:text-lightMuted dark:hover:text-darkMuted transition-colors">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-20 border-y-minimal border-lightBorder dark:border-darkBorder">
                <p class="display-font text-2xl md:text-4xl text-lightMuted dark:text-darkMuted mb-8">BELUM ADA PESANAN.</p>
                <a href="{{ url('/katalog') }}" class="inline-block border-b-2 border-lightMain dark:border-darkMain pb-1 text-[10px] tracking-[0.2em] uppercase font-bold hover:text-lightMuted dark:hover:text-darkMuted transition-colors">
                    Mulai Belanja
                </a>
            </div>
        @endif
    </main>

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
                if (html.classList.contains('dark')) {
                    localStorage.theme = 'dark';
                } else {
                    localStorage.theme = 'light';
                }
            });
        }
    </script>
</body>
</html>



