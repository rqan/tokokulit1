<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stores - ENY LEATHER®</title>
    
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
            <a href="{{ url('/') }}" class="display-font text-4xl tracking-tight">ENY LEATHER®</a>
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

    <!-- Main Content -->
    <main class="w-full min-h-screen py-20 px-8">
        <div class="max-w-6xl mx-auto">
            <!-- Navigation Links -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
                <a href="javascript:history.back()" class="text-sm font-semibold tracking-wider uppercase border border-lightBorder dark:border-darkBorder px-4 py-2 hover:bg-lightMain hover:text-lightBg dark:hover:bg-darkMain dark:hover:text-darkBg transition-colors mb-4 md:mb-0 inline-block w-max">
                    &larr; Back
                </a>
                <div class="flex space-x-4 text-xs font-semibold tracking-[0.2em] uppercase">
                    <a href="{{ url('/') }}" class="link-hover">Landing Page</a>
                    <span>/</span>
                    <a href="{{ url('/katalog') }}" class="link-hover">Katalog</a>
                </div>
            </div>

            <h1 class="display-font text-5xl md:text-7xl mb-12 text-center">Our Stores</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-16">
                <!-- Offline Stores -->
                <div>
                    <h2 class="text-2xl font-bold mb-6 tracking-wider uppercase border-b-minimal border-lightBorder dark:border-darkBorder pb-4">Offline Stores</h2>
                    <div class="space-y-8 mt-8">
                        @if(isset($offlineStores) && $offlineStores->count() > 0)
                            @foreach($offlineStores as $store)
                                <div class="p-6 border border-lightBorder dark:border-darkBorder rounded hover:shadow-lg transition-shadow">
                                    <h3 class="text-xl font-semibold mb-2">{{ $store->name }}</h3>
                                    <p class="text-lightMuted dark:text-darkMuted mb-2">{{ $store->description }}</p>
                                    <p class="text-sm">{{ $store->address }}</p>
                                    <a href="https://maps.google.com/?q={{ urlencode($store->name . ' ' . $store->address) }}" target="_blank" class="mt-6 inline-block text-[10px] font-bold uppercase tracking-wider underline hover:text-lightMuted dark:hover:text-darkMuted">View on Maps</a>
                                </div>
                            @endforeach
                        @else
                            <p>Offline stores not available.</p>
                        @endif
                    </div>
                </div>

                <!-- Online Stores -->
                <div>
                    <h2 class="text-2xl font-bold mb-6 tracking-wider uppercase border-b-minimal border-lightBorder dark:border-darkBorder pb-4">Online Official Stores</h2>
                    <div class="space-y-8 mt-8">
                        @if(isset($onlineStores) && $onlineStores->count() > 0)
                            @foreach($onlineStores as $store)
                                <a href="{{ $store->url ?? '#' }}" class="block p-6 border border-lightBorder dark:border-darkBorder rounded hover:bg-lightBorder dark:hover:bg-darkBorder transition-colors flex items-center justify-between" target="_blank">
                                    <div>
                                        <h3 class="text-xl font-semibold mb-1">{{ $store->name }}</h3>
                                        <p class="text-sm text-lightMuted dark:text-darkMuted">{{ $store->description }}</p>
                                    </div>
                                    <span class="text-2xl">&rarr;</span>
                                </a>
                            @endforeach
                        @else
                            <p>Online stores not available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full pt-16">
        <div class="w-full px-8 grid grid-cols-1 md:grid-cols-5 gap-8 mb-20 text-[10px] font-semibold tracking-widest uppercase text-lightMuted dark:text-darkMuted">
            <div class="md:col-span-2 pr-8">
                <h4 class="mb-4 text-lightMain dark:text-darkMain">Newsletter</h4>
                <div class="flex justify-between border-b-minimal border-lightBorder dark:border-darkBorder pb-2 mt-8">
                    <input type="email" placeholder="EMAIL ADDRESS" class="bg-transparent outline-none w-full placeholder-lightBorder dark:placeholder-darkBorder text-lightMain dark:text-darkMain">
                    <button class="hover:text-lightMain dark:hover:text-darkMain transition-colors">SUBSCRIBE</button>
                </div>
            </div>
            <div>
                <h4 class="mb-4 text-lightMain dark:text-darkMain">Support</h4>
                <ul class="space-y-3">
                    <li><a href="{{ url('/contact') }}" class="hover:text-lightMain dark:hover:text-darkMain">Contact Us</a></li>
                </ul>
            </div>
            <div>
                <h4 class="mb-4 text-lightMain dark:text-darkMain">Company</h4>
                <ul class="space-y-3">
                    <li><a href="{{ url('/about') }}" class="hover:text-lightMain dark:hover:text-darkMain">About</a></li>
                    <li><a href="{{ url('/stores') }}" class="hover:text-lightMain dark:hover:text-darkMain">Stores</a></li>
                </ul>
            </div>
            <div class="flex justify-between md:col-span-1">
                <div>
                    <h4 class="mb-4 text-lightMain dark:text-darkMain">Social</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="hover:text-lightMain dark:hover:text-darkMain">Instagram</a></li>
                        <li><a href="#" class="hover:text-lightMain dark:hover:text-darkMain">TikTok</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Massive Footer Logo -->
        <div class="w-full overflow-hidden text-center pb-2 border-t-minimal border-lightBorder dark:border-darkBorder pt-8">
            <h1 class="display-font text-[20vw] leading-none text-lightMain dark:text-darkMain select-none">ENY LEATHER®</h1>
        </div>
                    <div class="w-full flex justify-end px-8 py-4 text-[9px] font-bold tracking-[0.2em] uppercase text-lightMuted dark:text-darkMuted">
            <a href="https://mein-profile.vercel.app" target="_blank" rel="noopener noreferrer" class="hover:text-lightMain dark:hover:text-darkMain transition-colors">&copy; Copyright REGANDEWA 2026</a>
        </div>
</footer>

    <!-- Scripts -->
    <script>
        // Theme Toggle
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



