<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boutiques - TOKO RAFI</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind-config.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <!-- Leaflet CSS for Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
</head>
<body class="w-full relative antialiased bg-lightBg text-lightMain dark:bg-darkBg dark:text-darkMain min-h-screen flex flex-col">

    <!-- Header -->
    <header class="w-full px-8 py-6 flex justify-between items-center border-b-minimal border-lightBorder dark:border-darkBorder sticky top-0 z-40 bg-lightBg dark:bg-darkBg transition-colors">
        <div class="flex-1 flex justify-start">
            <a href="{{ url('/') }}" class="display-font text-4xl tracking-tight">TOKO RAFI</a>
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
            <a href="{{ url('/boutiques') }}" class="link-hover">Boutiques</a>
        </div>
        
        <div class="flex-1 flex justify-end items-center space-x-6 md:space-x-8 text-[10px] font-semibold tracking-[0.2em] uppercase">
            @if(session('isLoggedIn') || Auth::check())
                <a href="{{ url('/profile') }}" class="link-hover">Profile</a>
            @else
                <a href="{{ url('/login') }}" class="link-hover">Sign In</a>
            @endif
        </div>
    </header>

    <main class="flex-1 w-full max-w-7xl mx-auto px-8 py-12 md:py-20">
        <div class="text-center mb-16">
            <h4 class="text-[10px] tracking-[0.3em] uppercase font-bold text-lightMuted dark:text-darkMuted mb-4">Official Retailers</h4>
            <h1 class="display-font text-4xl lg:text-5xl leading-tight mb-6">FIND A BOUTIQUE</h1>
            <p class="text-sm leading-relaxed text-lightMuted dark:text-darkMuted max-w-xl mx-auto">
                Discover our collections at authorized retailers and TOKO RAFI
            </p>
        </div>

        <div class="flex flex-col lg:flex-row gap-12">
            <!-- Sidebar: List of Boutiques -->
            <div class="w-full lg:w-1/3 space-y-6">
                @forelse($boutiques as $boutique)
                <div class="p-6 border border-lightBorder dark:border-darkBorder hover:border-lightMain dark:hover:border-darkMain cursor-pointer transition-colors" onclick="focusMap({{ $boutique->latitude }}, {{ $boutique->longitude }}, '{{ addslashes($boutique->name) }}')">
                    <h3 class="display-font text-2xl mb-2">{{ $boutique->name }}</h3>
                    <p class="text-sm text-lightMuted dark:text-darkMuted mb-4">{{ $boutique->address }}</p>
                    <div class="text-xs tracking-widest uppercase font-semibold space-y-2">
                        @if($boutique->phone)<p>Tel: {{ $boutique->phone }}</p>@endif
                        @if($boutique->operating_hours)<p>Hours: {{ $boutique->operating_hours }}</p>@endif
                    </div>
                </div>
                @empty
                <div class="p-6 border border-lightBorder dark:border-darkBorder">
                    <p class="text-sm text-lightMuted dark:text-darkMuted text-center">No boutiques found in the database.</p>
                </div>
                @endforelse
            </div>

            <!-- Map Area -->
            <div class="w-full lg:w-2/3 min-h-[500px] border border-lightBorder dark:border-darkBorder relative bg-gray-100 dark:bg-gray-800" id="map">
                <!-- Map will be rendered here -->
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full pt-16">
        <div class="w-full overflow-hidden text-center pb-2 pt-8">
            <h1 class="display-font text-[10vw] leading-none text-lightMain dark:text-darkMain select-none">TOKO RAFI</h1>
        </div>
                <div class="w-full flex justify-end px-8 py-4 text-[9px] font-bold tracking-[0.2em] uppercase text-lightMuted dark:text-darkMuted">
            <a href="https://mein-profile.vercel.app" target="_blank" rel="noopener noreferrer" class="hover:text-lightMain dark:hover:text-darkMain transition-colors">&copy; Copyright REGANDEWA 2026</a>
        </div>
    </footer>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        var map = L.map('map').setView([-6.2088, 106.8456], 5); // Default to Indonesia/Asia view

        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        var markers = [];

        @foreach($boutiques as $boutique)
            @if($boutique->latitude && $boutique->longitude)
                var marker = L.marker([{{ $boutique->latitude }}, {{ $boutique->longitude }}]).addTo(map)
                    .bindPopup("<b>{{ addslashes($boutique->name) }}</b><br/>{{ addslashes($boutique->address) }}");
                markers.push(marker);
            @endif
        @endforeach

        if(markers.length > 0) {
            var group = new L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.1));
        }

        function focusMap(lat, lng, name) {
            if(lat && lng) {
                map.setView([lat, lng], 15);
            }
        }
    </script>
</body>
</html>



