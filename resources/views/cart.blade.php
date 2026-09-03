<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind-config.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ux.css') }}">
    <script src="{{ asset('js/ux.js') }}"></script>
</head>
<body class="w-full relative antialiased selection:bg-lightMain selection:text-lightBg dark:selection:bg-darkMain dark:selection:text-darkBg bg-lightBg text-lightMain dark:bg-darkBg dark:text-darkMain">

    <!-- Minimalist Dark/Light Toggle -->
    

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
                <a href="{{ url('/profile') }}" class="link-hover">Profile</a>
                <button id="themeToggle" class="link-hover uppercase focus:outline-none">Theme</button>
                <a href="{{ url('/logout') }}" class="link-hover text-red-500">Logout</a>
            @else
                <button id="themeToggle" class="link-hover uppercase focus:outline-none">Theme</button>
                <a href="{{ url('/login') }}" class="link-hover">Sign In</a>
            @endif
        </div>
    </header>

    <!-- Cart Section -->
    <main class="w-full min-h-[70vh] px-8 py-16">
        <h1 class="display-font text-5xl md:text-6xl mb-12 uppercase">Keranjang Anda</h1>

        @if(session('cart') && count(session('cart')) > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Cart Items -->
                <div class="lg:col-span-2 space-y-8">
                    @php $total = 0; @endphp
                    @foreach(session('cart') as $id => $details)
                        @php $total += $details['product']['price'] * $details['quantity']; @endphp
                        <div class="flex flex-col sm:flex-row gap-6 pb-8 border-b-minimal border-lightBorder dark:border-darkBorder">
                            <!-- Checkbox -->
                            <div class="flex items-center sm:items-start pt-2">
                                <input type="checkbox" value="{{ $id }}" data-price="{{ $details['product']['price'] * $details['quantity'] }}" class="item-checkbox w-5 h-5 accent-lightMain dark:accent-darkMain cursor-pointer" checked onchange="updateCartTotal()">
                            </div>
                            <!-- Image -->
                            <div class="w-full sm:w-32 aspect-[3/2] bg-[#E5E5E5] dark:bg-[#1E1E1E] flex items-center justify-center shrink-0">
                                @if(!empty($details['product']['image_url']))
                                    <img src="{{ $details['product']['image_url'] }}" alt="{{ $details['product']['name'] }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <!-- Details -->
                            <div class="flex-1 flex flex-col justify-between">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="display-font text-xl uppercase mb-2">{{ $details['product']['name'] }}</h3>
                                        @if(!empty($details['size']))
                                        <p class="text-[10px] tracking-widest text-lightMuted dark:text-darkMuted mb-2">Ukuran: {{ $details['size'] }}</p>
                                        @endif
                                        <p class="text-[10px] tracking-widest uppercase text-lightMuted dark:text-darkMuted">Rp{{ number_format($details['product']['price'], 0, ',', '.') }}</p>
                                    </div>
                                    <form action="{{ url('/pelanggan/cart/remove') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $id }}">
                                        <button type="submit" class="text-[10px] tracking-widest uppercase text-red-500 hover:text-red-700 transition-colors">Hapus</button>
                                    </form>
                                </div>
                                <div class="flex justify-between items-end mt-6">
                                    <form action="{{ url('/pelanggan/cart/update') }}" method="POST" class="flex items-center border border-lightBorder dark:border-darkBorder">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $id }}">
                                        <button type="button" class="w-8 h-8 flex items-center justify-center text-lg hover:bg-lightBorder dark:hover:bg-darkBorder transition-colors" onclick="this.nextElementSibling.stepDown(); this.form.submit()">-</button>
                                        <input type="number" name="quantity" value="{{ $details['quantity'] }}" min="1" class="w-12 h-8 text-center bg-transparent border-x border-lightBorder dark:border-darkBorder text-[10px] tracking-widest outline-none appearance-none" onchange="this.form.submit()">
                                        <button type="button" class="w-8 h-8 flex items-center justify-center text-lg hover:bg-lightBorder dark:hover:bg-darkBorder transition-colors" onclick="this.previousElementSibling.stepUp(); this.form.submit()">+</button>
                                    </form>
                                    <p class="font-bold text-sm tracking-widest uppercase">Rp{{ number_format($details['product']['price'] * $details['quantity'], 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Summary -->
                <div class="lg:col-span-1">
                    <div class="border border-lightBorder dark:border-darkBorder p-8 sticky top-32">
                        <h2 class="display-font text-3xl mb-6 uppercase">Ringkasan</h2>
                        <div class="flex justify-between items-center mb-4 text-[10px] tracking-widest uppercase text-lightMuted dark:text-darkMuted">
                            <span>Subtotal</span>
                            <span class="cart-total-display">Rp{{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-8 text-[10px] tracking-widest uppercase text-lightMuted dark:text-darkMuted">
                            <span>Pengiriman</span>
                            <span>Dihitung di checkout</span>
                        </div>
                        <div class="flex justify-between items-center py-6 border-t-minimal border-lightBorder dark:border-darkBorder font-bold text-lg tracking-widest uppercase">
                            <span>Total Estimasi</span>
                            <span class="cart-total-display">Rp{{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <button onclick="processCheckout(event)" class="w-full py-4 bg-lightMain dark:bg-darkMain text-lightBg dark:text-darkBg text-center text-[10px] tracking-[0.2em] uppercase font-bold hover:opacity-90 transition-opacity">
                            Lanjut ke Checkout
                        </button>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-20 border-y-minimal border-lightBorder dark:border-darkBorder">
                <p class="display-font text-2xl md:text-4xl text-lightMuted dark:text-darkMuted mb-8">KERANJANG ANDA KOSONG</p>
                <a href="{{ url('/katalog') }}" class="inline-block border-b-2 border-lightMain dark:border-darkMain pb-1 text-[10px] tracking-[0.2em] uppercase font-bold hover:text-lightMuted dark:hover:text-darkMuted transition-colors">
                    Belanja Sekarang
                </a>
            </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="w-full pt-16">
        <div class="w-full overflow-hidden text-center pb-2 border-t-minimal border-lightBorder dark:border-darkBorder pt-8">
            <h1 class="display-font text-[20vw] leading-none text-lightMain dark:text-darkMain select-none">ENY LEATHER</h1>
        </div>
                    <div class="w-full flex justify-end px-8 py-4 text-[9px] font-bold tracking-[0.2em] uppercase text-lightMuted dark:text-darkMuted">
            <a href="https://mein-profile.vercel.app" target="_blank" rel="noopener noreferrer" class="hover:text-lightMain dark:hover:text-darkMain transition-colors">&copy; Copyright REGANDEWA 2026</a>
        </div>
</footer>

    <script>
        function updateCartTotal() {
            let total = 0;
            const checkboxes = document.querySelectorAll('.item-checkbox:checked');
            checkboxes.forEach(cb => {
                total += parseInt(cb.getAttribute('data-price'));
            });
            
            const formattedTotal = new Intl.NumberFormat('id-ID').format(total);
            const totalElements = document.querySelectorAll('.cart-total-display');
            totalElements.forEach(el => {
                el.innerText = 'Rp' + formattedTotal;
            });
        }

        function processCheckout(e) {
            e.preventDefault();
            const selected = document.querySelectorAll('.item-checkbox:checked');
            if(selected.length === 0) {
                alert('Pilih setidaknya satu produk untuk di-checkout.');
                return;
            }
            
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = "{{ url('/pelanggan/checkout') }}";
            
            selected.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_items[]';
                input.value = cb.value;
                form.appendChild(input);
            });
            
            document.body.appendChild(form);
            form.submit();
        }

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





