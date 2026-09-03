<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind-config.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ux.css') }}">
    <script src="{{ asset('js/ux.js') }}"></script>
</head>
<body class="w-full relative antialiased selection:bg-lightMain selection:text-lightBg dark:selection:bg-darkMain dark:selection:text-darkBg bg-lightBg text-lightMain dark:bg-darkBg dark:text-darkMain">

    <!-- Header -->
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
                <a href="{{ url('/pelanggan/wishlist') }}" class="link-hover">Wishlist</a>
                <a href="{{ url('/profile') }}" class="link-hover">Profile</a>
                <button id="themeToggle" class="link-hover uppercase focus:outline-none">Theme</button>
                <a href="{{ url('/logout') }}" class="link-hover text-red-500">Logout</a>
            @else
                <button id="themeToggle" class="link-hover uppercase focus:outline-none">Theme</button>
                <a href="{{ url('/login') }}" class="link-hover">Sign In</a>
            @endif
        </div>
    </header>

    <!-- Wishlist Section -->
    <main class="w-full min-h-[70vh] px-8 py-16">
        <h1 class="display-font text-5xl md:text-6xl mb-12 uppercase">Wishlist Anda</h1>

        @if(session('success'))
            <div class="p-4 mb-8 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if($wishlists->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($wishlists as $item)
                    <div class="group cursor-pointer">
                        <div class="relative w-full aspect-[3/4] bg-[#f0f0f0] dark:bg-[#1a1a1a] overflow-hidden">
                            @if($item->product->image_url)
                                <img src="{{ asset($item->product->image_url) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out filter grayscale hover:grayscale-0">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-xs tracking-widest uppercase opacity-30">No Image</div>
                            @endif
                            <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </div>
                        <div class="mt-4 flex flex-col items-center text-center">
                            <h2 class="text-xs font-bold tracking-[0.1em] uppercase mb-1">{{ $item->product->name }}</h2>
                            <p class="text-[10px] tracking-widest text-lightSecondary dark:text-darkSecondary mb-2">Rp {{ number_format($item->product->price, 0, ',', '.') }}</p>
                            
                            <div class="flex space-x-2 mt-2">
                                <a href="{{ url('/product/' . $item->product->id) }}" class="text-[10px] border border-lightMain dark:border-darkMain px-4 py-2 hover:bg-lightMain hover:text-lightBg dark:hover:bg-darkMain dark:hover:text-darkBg transition-colors uppercase tracking-widest">Lihat Detail</a>
                                <form action="{{ route('wishlist.destroy', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[10px] text-red-500 border border-red-500 px-4 py-2 hover:bg-red-500 hover:text-white transition-colors uppercase tracking-widest">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="w-full flex flex-col items-center justify-center py-20 border-t-minimal border-b-minimal border-lightBorder dark:border-darkBorder">
                <p class="text-sm tracking-[0.2em] uppercase mb-6 text-lightSecondary dark:text-darkSecondary">Wishlist Anda kosong.</p>
                <a href="{{ url('/katalog') }}" class="text-xs border border-lightMain dark:border-darkMain px-8 py-4 uppercase tracking-widest hover:bg-lightMain hover:text-lightBg dark:hover:bg-darkMain dark:hover:text-darkBg transition-colors">
                    Lihat Koleksi
                </a>
            </div>
        @endif
    </main>

    <!-- Footer Minimalis -->
    <footer class="w-full px-8 py-12 border-t-minimal border-lightBorder dark:border-darkBorder flex flex-col md:flex-row justify-between items-center text-[10px] font-semibold tracking-[0.2em] uppercase text-lightSecondary dark:text-darkSecondary">
        <div>© 2026 ENY LEATHER INC.</div>
        <div class="flex space-x-8 mt-4 md:mt-0">
            <a href="#" class="link-hover">Instagram</a>
            <a href="#" class="link-hover">Twitter</a>
        </div>
    </footer>

</body>
</html>


