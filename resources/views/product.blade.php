<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product['name'] }} - TOKO RAFI</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind-config.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ux.css') }}">
    <script src="{{ asset('js/ux.js') }}"></script>
</head>
<body class="w-full relative antialiased selection:bg-lightMain selection:text-lightBg dark:selection:bg-darkMain dark:selection:text-darkBg bg-lightBg text-lightMain dark:bg-darkBg dark:text-darkMain min-h-screen flex flex-col">

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

    <main class="flex-1 flex flex-col md:flex-row w-full max-w-[1800px] mx-auto border-b border-lightBorder dark:border-darkBorder">
        
        <!-- Left: Image Gallery Area -->
        <div id="image-container" class="flex-1 relative bg-[#F9F9F9] dark:bg-[#111] overflow-hidden flex items-center justify-center min-h-[50vh] md:min-h-0">
            
            <!-- Expand Icon -->
            <button onclick="document.getElementById('imageModal').classList.remove('hidden')" class="absolute top-6 right-6 w-10 h-10 rounded-full bg-lightBg/50 dark:bg-darkBg/50 backdrop-blur-md flex items-center justify-center border border-lightBorder dark:border-darkBorder transition-transform z-10 hover:scale-110">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
            </button>

            <!-- Image -->
            <img src="{{ $product['image_url'] ? $product['image_url'] : '/images/products/biker_jacket.jpg' }}" alt="{{ $product['name'] }}" class="w-full max-w-3xl aspect-[3/2] object-cover drop-shadow-2xl relative z-0">

            <!-- Dots (simulated) -->
            <div class="absolute bottom-8 left-8 flex space-x-3">
                <div class="w-1.5 h-1.5 rounded-full bg-lightMain dark:bg-darkMain"></div>
                <div class="w-1.5 h-1.5 rounded-full bg-lightBorder dark:bg-darkBorder"></div>
                <div class="w-1.5 h-1.5 rounded-full bg-lightBorder dark:bg-darkBorder"></div>
                <div class="w-1.5 h-1.5 rounded-full bg-lightBorder dark:bg-darkBorder"></div>
            </div>

            <!-- Arrows (simulated) -->
            <div class="absolute bottom-8 right-8 flex space-x-2">
                <button class="w-12 h-12 rounded-full border border-lightBorder dark:border-darkBorder flex items-center justify-center hover:bg-lightMain dark:hover:bg-darkMain hover:text-lightBg dark:hover:text-darkBg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <button class="w-12 h-12 rounded-full border border-lightBorder dark:border-darkBorder flex items-center justify-center hover:bg-lightMain dark:hover:bg-darkMain hover:text-lightBg dark:hover:text-darkBg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
        </div>

        <!-- Right: Info Area -->
        <div class="w-full md:w-[450px] lg:w-[500px] flex flex-col p-8 md:p-12 lg:p-16 justify-center bg-lightBg dark:bg-darkBg shrink-0">
            
            <div class="mb-8">
                <button onclick="goBack()" class="flex items-center text-[10px] uppercase tracking-[0.2em] font-bold text-lightSecondary dark:text-darkSecondary hover:text-lightMain dark:hover:text-darkMain transition-colors mb-8 group">
                    <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
                </button>
                <h4 class="text-[10px] tracking-[0.3em] uppercase font-bold text-lightMuted dark:text-darkMuted mb-4">{{ $product['category'] ?? 'COLLECTION' }}</h4>
                <h1 class="display-font text-4xl lg:text-5xl leading-tight mb-6">{{ $product['name'] }}</h1>
                <p class="text-sm leading-relaxed text-lightMuted dark:text-darkMuted">
                    {{ $product['description'] }}
                </p>
            </div>

            <div class="mb-10 border-t border-b border-lightBorder dark:border-darkBorder py-6">
                <div class="flex items-center justify-between">
                    <span class="text-xs uppercase tracking-widest font-semibold">Standard Color</span>
                    <div class="flex space-x-3">
                        <button class="w-6 h-6 rounded-full border-2 border-lightMain dark:border-darkMain flex items-center justify-center p-0.5 focus:outline-none">
                            <span class="w-full h-full rounded-full bg-[#1A1A1A] block"></span>
                        </button>
                        <button class="w-6 h-6 rounded-full border border-lightBorder dark:border-darkBorder flex items-center justify-center p-0.5 focus:outline-none hover:border-lightMuted">
                            <span class="w-full h-full rounded-full bg-[#F5F5F5] border border-gray-300 block"></span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mb-10">
                <h2 class="display-font text-3xl">Rp{{ number_format($product['price'], 0, ',', '.') }}</h2>
            </div>

            <div class="space-y-4">
                @if(session('isLoggedIn') || Auth::check())
                <form action="{{ url('pelanggan/cart/add') }}" method="POST" class="w-full space-y-6" onsubmit="submitProductCartForm(event)">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                    
                    @if(!empty($product['sizes']))
                    <!-- Ukuran -->
                    <div class="flex flex-col space-y-2">
                        <label for="size" class="text-xs uppercase tracking-widest font-semibold">Ukuran</label>
                        <select name="size" id="size" class="w-full bg-transparent border border-lightBorder dark:border-darkBorder rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors">
                            <option value="" class="bg-lightBg dark:bg-darkBg text-lightMain dark:text-darkMain">Pilih Ukuran (opsional)</option>
                            @foreach($product['sizes'] as $s)
                            <option value="{{ $s }}" class="bg-lightBg dark:bg-darkBg text-lightMain dark:text-darkMain">{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    @if(!empty($product['colors']))
                    <!-- Warna -->
                    <div class="flex flex-col space-y-2">
                        <label for="color" class="text-xs uppercase tracking-widest font-semibold">Warna</label>
                        <select name="color" id="color" class="w-full bg-transparent border border-lightBorder dark:border-darkBorder rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors">
                            <option value="" class="bg-lightBg dark:bg-darkBg text-lightMain dark:text-darkMain">Pilih Warna (opsional)</option>
                            @foreach($product['colors'] as $c)
                            <option value="{{ $c }}" class="bg-lightBg dark:bg-darkBg text-lightMain dark:text-darkMain">{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- Jumlah -->
                    <div class="flex flex-col space-y-2">
                        <label for="quantity" class="text-xs uppercase tracking-widest font-semibold">Jumlah</label>
                        <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product['stock'] }}" class="w-full bg-transparent border border-lightBorder dark:border-darkBorder rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors">
                    </div>

                    <button type="submit" id="submitBtn" class="w-full py-4 px-6 rounded-full border border-lightMain dark:border-darkMain text-xs uppercase tracking-[0.2em] font-bold hover:bg-lightMain hover:text-lightBg dark:hover:bg-darkMain dark:hover:text-darkBg transition-colors mt-4">
                        Tambahkan ke Keranjang
                    </button>
                </form>

                <form action="{{ route('wishlist.store') }}" method="POST" class="w-full">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                    <button type="submit" class="w-full py-4 px-6 rounded-full border border-lightBorder dark:border-darkBorder text-xs uppercase tracking-[0.2em] font-bold hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors mt-2 flex justify-center items-center gap-2">
                        <span>â™¡ Tambahkan ke Wishlist</span>
                    </button>
                </form>

                <!-- Book Private Viewing Button -->
                <button onclick="document.getElementById('bookingModal').classList.remove('hidden')" class="w-full py-4 px-6 rounded-full bg-lightMain text-lightBg dark:bg-darkMain dark:text-darkBg text-xs uppercase tracking-[0.2em] font-bold hover:opacity-90 transition-opacity mt-4">
                    Book a Private Viewing
                </button>

                <!-- Booking Modal -->
                <div id="bookingModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm px-4">
                    <div class="bg-lightBg dark:bg-darkBg p-8 md:p-12 max-w-lg w-full relative">
                        <button onclick="document.getElementById('bookingModal').classList.add('hidden')" class="absolute top-6 right-6 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                        <h2 class="display-font text-3xl mb-2">Private Appointment</h2>
                        <p class="text-sm text-lightMuted dark:text-darkMuted mb-8">Schedule a private viewing for {{ $product['name'] }} with our concierge.</p>

                        <form action="{{ route('appointments.book', $product['id']) }}" method="POST" class="space-y-6">
                            @csrf
                            <div>
                                <label for="appointment_date" class="text-xs uppercase tracking-widest font-semibold block mb-2">Preferred Date</label>
                                <input type="date" name="appointment_date" id="appointment_date" required class="w-full bg-transparent border border-lightBorder dark:border-darkBorder rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors">
                            </div>
                            <div>
                                <label for="appointment_time" class="text-xs uppercase tracking-widest font-semibold block mb-2">Preferred Time</label>
                                <input type="time" name="appointment_time" id="appointment_time" required class="w-full bg-transparent border border-lightBorder dark:border-darkBorder rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors">
                            </div>
                            <div>
                                <label for="notes" class="text-xs uppercase tracking-widest font-semibold block mb-2">Special Requests (Optional)</label>
                                <textarea name="notes" id="notes" rows="3" class="w-full bg-transparent border border-lightBorder dark:border-darkBorder rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors"></textarea>
                            </div>
                            <button type="submit" class="w-full py-4 px-6 rounded-full bg-lightMain text-lightBg dark:bg-darkMain dark:text-darkBg text-xs uppercase tracking-[0.2em] font-bold hover:opacity-90 transition-opacity">
                                Confirm Request
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <a href="{{ url('/login') }}" class="block text-center w-full py-4 px-6 rounded-full border border-lightMain dark:border-darkMain text-xs uppercase tracking-[0.2em] font-bold hover:bg-lightMain hover:text-lightBg dark:hover:bg-darkMain dark:hover:text-darkBg transition-colors">
                    Login untuk Menambah ke Keranjang
                </a>
                @endif
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

    <!-- Image Modal for Lightbox -->
    <div id="imageModal" class="hidden fixed inset-0 z-[120] flex items-center justify-center bg-black/90 backdrop-blur-sm p-4 md:p-12 cursor-zoom-out" onclick="this.classList.add('hidden')">
        <button class="absolute top-8 right-8 text-white focus:outline-none hover:scale-110 transition-transform">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        <img src="{{ $product['image_url'] ? $product['image_url'] : '/images/products/biker_jacket.jpg' }}" alt="{{ $product['name'] }}" class="max-w-full max-h-full object-contain drop-shadow-2xl cursor-default" onclick="event.stopPropagation()">
    </div>

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

    <!-- Toast Notification -->
    <div id="toastNotif" class="fixed bottom-6 right-6 z-[110] bg-lightMain dark:bg-darkMain text-lightBg dark:text-darkBg px-6 py-4 translate-y-24 opacity-0 transition-all duration-300 flex items-center space-x-3 shadow-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span class="text-xs font-bold tracking-widest uppercase">Berhasil ditambahkan ke keranjang!</span>
    </div>

    <script>
        function submitProductCartForm(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const btn = document.getElementById('submitBtn');
            const originalText = btn.innerText;
            btn.innerText = 'Menambahkan...';
            btn.disabled = true;

            fetch("{{ url('pelanggan/cart/add') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                btn.innerText = originalText;
                btn.disabled = false;
                                if (data.cart_count !== undefined) {
                    const cartLinks = document.querySelectorAll('a[href*="/pelanggan/cart"]');
                    cartLinks.forEach(link => {
                        link.innerText = 'Cart (' + data.cart_count + ')';
                    });
                }
                showToast();
            })
            .catch(err => {
                console.error(err);
                btn.innerText = originalText;
                btn.disabled = false;
                alert('Terjadi kesalahan.');
            });
        }

        function showToast() {
            const toast = document.getElementById('toastNotif');
            toast.classList.remove('translate-y-24', 'opacity-0');
            setTimeout(() => {
                toast.classList.add('translate-y-24', 'opacity-0');
            }, 3000);
        }

        function goBack() {
            if (document.referrer.indexOf(window.location.host) !== -1) {
                history.back();
            } else {
                window.location.href = "{{ url('/katalog') }}";
            }
        }
    </script>
</body>
</html>





