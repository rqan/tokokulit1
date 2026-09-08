<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk</title>
    
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
                <a href="{{ url('/profile') }}" class="link-hover">Profile</a>
                <button id="themeToggle" class="link-hover uppercase focus:outline-none">Theme</button>
                <a href="{{ url('/logout') }}" class="link-hover text-red-500">Logout</a>
            @else
                <button id="themeToggle" class="link-hover uppercase focus:outline-none">Theme</button>
                <a href="{{ url('/login') }}" class="link-hover">Sign In</a>
            @endif
        </div>
    </header>

    <!-- Hero Section (Carousel) -->
    <section class="w-full h-[60vh] md:h-[80vh] relative overflow-hidden group border-b-minimal border-lightBorder dark:border-darkBorder">
        <!-- Carousel Inner -->
        <div id="carousel-inner" class="flex w-full h-full transition-transform duration-700 ease-in-out">
            <!-- Slide 1 -->
            <div class="min-w-full h-full relative">
                <img src="/images/products/biker_jacket.jpg" class="w-full h-full object-cover grayscale opacity-80" alt="Slide 1">
                <div class="absolute inset-0 bg-black/40 mix-blend-multiply"></div>
                <div class="absolute inset-0 flex items-center justify-center text-center p-8 z-10">
                    <div class="relative z-20">
                        <h1 class="display-font text-[12vw] md:text-[10vw] mb-4 text-[#DDE2C6]">DESIGN & TECH</h1>
                        <a href="{{ url('/katalog') }}" class="inline-block border-b-2 border-white pb-1 text-[10px] tracking-[0.2em] uppercase font-bold hover:text-gray-300 text-white transition-colors cursor-pointer relative z-30">Shop Now</a>
                    </div>
                </div>
            </div>
            <!-- Slide 2 -->
            <div class="min-w-full h-full relative">
                <img src="/images/products/leather_boots.jpg" class="w-full h-full object-cover grayscale opacity-80" alt="Slide 2">
                <div class="absolute inset-0 bg-black/40 mix-blend-multiply"></div>
                <div class="absolute inset-0 flex items-center justify-center text-center p-8 z-10">
                    <div class="relative z-20">
                        <h1 class="display-font text-[12vw] md:text-[10vw] mb-4 text-[#DDE2C6]">ELEGANCE</h1>
                        <a href="{{ url('/katalog') }}" class="inline-block border-b-2 border-white pb-1 text-[10px] tracking-[0.2em] uppercase font-bold hover:text-gray-300 text-white transition-colors cursor-pointer relative z-30">Explore Collection</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Controls -->
        <button id="prevBtn" class="absolute left-2 md:left-6 top-1/2 -translate-y-1/2 w-12 h-12 md:w-14 md:h-14 flex items-center justify-center rounded-full bg-black/20 hover:bg-black/40 backdrop-blur-md border border-white/20 text-white opacity-100 md:opacity-0 group-hover:opacity-100 hover:scale-110 transition-all duration-300 z-10 shadow-lg" aria-label="Previous Slide">
            <svg class="w-6 h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        <button id="nextBtn" class="absolute right-2 md:right-6 top-1/2 -translate-y-1/2 w-12 h-12 md:w-14 md:h-14 flex items-center justify-center rounded-full bg-black/20 hover:bg-black/40 backdrop-blur-md border border-white/20 text-white opacity-100 md:opacity-0 group-hover:opacity-100 hover:scale-110 transition-all duration-300 z-10 shadow-lg" aria-label="Next Slide">
            <svg class="w-6 h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
    </section>

    <!-- Product Horizontal Scroll -->
    <section id="produk" class="w-full overflow-hidden">

        <!-- Section Header -->
        <div class="w-full px-8 py-8 flex items-end justify-between border-b-minimal border-lightBorder dark:border-darkBorder">
            <h2 class="display-font text-5xl md:text-6xl leading-none">COLLECTION</h2>
            <div class="hidden md:flex items-center gap-3 text-[10px] tracking-[0.2em] uppercase text-lightMuted dark:text-darkMuted">
                <span>Drag or scroll to explore</span>
                <svg class="w-10 h-px" viewBox="0 0 40 1" fill="none"><line x1="0" y1="0.5" x2="40" y2="0.5" stroke="currentColor"/></svg>
            </div>
        </div>

        <!-- Scroll Wrapper with fade edges -->
        <div class="relative">
            <!-- Left fade -->
            <div class="pointer-events-none absolute left-0 top-0 h-full w-16 z-10 bg-gradient-to-r from-lightBg dark:from-darkBg to-transparent"></div>
            <!-- Right fade -->
            <div class="pointer-events-none absolute right-0 top-0 h-full w-16 z-10 bg-gradient-to-l from-lightBg dark:from-darkBg to-transparent"></div>

            <!-- Scrollable track -->
            <div
                id="product-scroll"
                class="flex overflow-x-auto gap-0 scroll-smooth snap-x snap-mandatory cursor-grab active:cursor-grabbing select-none"
                style="scrollbar-width: none; -ms-overflow-style: none;"
            >
                @if (!empty($products) && is_iterable($products) && count($products) > 0)
                    @foreach ($products as $product)
                    <!-- Card -->
                    <div class="group flex-none snap-start w-[60vw] sm:w-[35vw] md:w-[25vw] lg:w-[18vw] flex flex-col cursor-pointer border-r-minimal border-lightBorder dark:border-darkBorder transition-all">
                        <a href="{{ url('/product/' . $product['id']) }}" class="block h-full flex flex-col">
                            <!-- Image -->
                            <div class="w-full aspect-[3/2] bg-[#E5E5E5] dark:bg-[#1E1E1E] overflow-hidden flex items-center justify-center transition-colors relative">
                                @if(!empty($product['image_url']))
                                    <img
                                        src="{{ $product['image_url'] }}"
                                        alt="{{ $product['name'] }}"
                                        draggable="false"
                                        class="object-cover w-full h-full grayscale group-hover:grayscale-0 group-hover:scale-105 transition-all duration-500 pointer-events-none"
                                    >
                                @else
                                    <span class="display-font text-2xl text-lightBorder dark:text-darkBorder">NO IMAGE</span>
                                @endif
                            </div>

                            <!-- Info -->
                            <div class="p-5 flex justify-between items-start border-t-minimal border-lightBorder dark:border-darkBorder flex-1">
                            <div class="flex-1 min-w-0 pr-4">
                                <h3 class="display-font text-xl mb-1 text-lightMain dark:text-darkMain truncate">{{ $product['name'] }}</h3>
                                <p class="text-[9px] tracking-widest uppercase text-lightMuted dark:text-darkMuted truncate">{{ $product['category'] ?? $product['description'] }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="display-font text-lg text-lightMain dark:text-darkMain">
                                    Rp{{ number_format($product['price'], 0, ',', '.') }}
                                </div>
                            </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                @else
                    <div class="w-full text-center py-32 text-lightMuted dark:text-darkMuted display-font text-4xl">
                        BELUM ADA PRODUK.
                    </div>
                @endif
            </div>
        </div>

        <!-- Scroll progress bar -->
        <div class="w-full px-8 py-5 flex items-center gap-4">
            <div class="flex-1 h-px bg-lightBorder dark:bg-darkBorder relative overflow-hidden rounded-full">
                <div id="scroll-progress" class="absolute left-0 top-0 h-full bg-lightMain dark:bg-darkMain rounded-full transition-all duration-100" style="width: 0%"></div>
            </div>
            <span id="scroll-counter" class="text-[10px] tracking-widest uppercase text-lightMuted dark:text-darkMuted font-semibold shrink-0">01 / {{ count($products) }}</span>
        </div>
    </section>


    <!-- Rating & Ulasan Pelanggan -->
    <section id="ulasan" class="w-full overflow-hidden border-b-minimal border-lightBorder dark:border-darkBorder pb-16 pt-8">
        <div class="w-full px-8 py-8 flex flex-col md:flex-row items-end justify-between">
            <h2 class="display-font text-5xl md:text-6xl leading-none">ULASAN PELANGGAN</h2>
            <!-- Note: Controller needs to pass $ratings variable -->
            @php
                $avgRating = $ratings->avg('rating') ?? 0;
            @endphp
            <div class="flex items-center gap-4 mt-4 md:mt-0">
                <div class="flex text-yellow-500 text-2xl">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($avgRating))
                            <span>&#9733;</span>
                        @else
                            <span class="text-gray-300 dark:text-gray-700">&#9733;</span>
                        @endif
                    @endfor
                </div>
                <span class="text-[10px] tracking-[0.2em] uppercase font-bold">{{ number_format($avgRating, 1) }} / 5.0</span>
            </div>
        </div>
        
        <div class="w-full px-8 mt-8">
            @if(isset($ratings) && $ratings->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @foreach($ratings as $rating)
                    <div class="p-6 border border-lightBorder dark:border-darkBorder flex flex-col justify-between">
                        <div>
                            <div class="flex text-yellow-500 mb-4">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $rating->rating)
                                        <span>&#9733;</span>
                                    @else
                                        <span class="text-gray-300 dark:text-gray-700">&#9733;</span>
                                    @endif
                                @endfor
                            </div>
                            <p class="text-sm italic mb-6">"{{ $rating->review ?? 'Tidak ada ulasan tertulis.' }}"</p>
                        </div>
                        <div class="flex justify-between items-center text-[10px] tracking-[0.2em] uppercase text-lightMuted dark:text-darkMuted border-t-minimal border-lightBorder dark:border-darkBorder pt-4 mt-4">
                            @php
                                $userName = $rating->user?->name ?? 'Anonymous';
                                $firstName = explode(' ', $userName)[0];
                                $maskedName = substr($firstName, 0, 1) . '***' . (strlen($firstName) > 2 ? substr($firstName, -1) : '');
                            @endphp
                            <span>{{ $maskedName }}</span>
                            <span>{{ $rating->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="w-full text-center py-16 text-lightMuted dark:text-darkMuted display-font text-2xl">
                    BELUM ADA ULASAN.
                </div>
            @endif
        </div>
    </section>

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
                    <li><a href="/contact" class="hover:text-lightMain dark:hover:text-darkMain">Contact Us</a></li>
                </ul>
            </div>
            <div>
                <h4 class="mb-4 text-lightMain dark:text-darkMain">Company</h4>
                <ul class="space-y-3">
                    <li><a href="/about" class="hover:text-lightMain dark:hover:text-darkMain">About</a></li>
                    <li><a href="/stores" class="hover:text-lightMain dark:hover:text-darkMain">Stores</a></li>
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
            <h1 class="display-font text-[20vw] leading-none text-lightMain dark:text-darkMain select-none">TOKO RAFI</h1>
        </div>
                    <div class="w-full flex justify-end px-8 py-4 text-[9px] font-bold tracking-[0.2em] uppercase text-lightMuted dark:text-darkMuted">
            <a href="https://mein-profile.vercel.app" target="_blank" rel="noopener noreferrer" class="hover:text-lightMain dark:hover:text-darkMain transition-colors">&copy; Copyright REGANDEWA 2026</a>
        </div>
</footer>

    <!-- Scripts -->
    <script>
        // =============================================
        // PRODUCT â€” Drag-to-scroll + Progress bar
        // =============================================
        const track = document.getElementById('product-scroll');
        const progress = document.getElementById('scroll-progress');
        const counter = document.getElementById('scroll-counter');
        const totalProducts = {{ count($products) }};

        let isDragging = false, startX, scrollLeft;

        if (track) {
            // Drag to scroll (mouse)
            track.addEventListener('mousedown', (e) => {
                isDragging = true;
                track.classList.add('active:cursor-grabbing');
                track.classList.remove('scroll-smooth', 'snap-x', 'snap-mandatory'); // Non-stutter drag
                startX = e.pageX - track.offsetLeft;
                scrollLeft = track.scrollLeft;
            });
            document.addEventListener('mouseup', () => { 
                if (isDragging) {
                    isDragging = false;
                    track.classList.remove('active:cursor-grabbing');
                    track.classList.add('scroll-smooth', 'snap-x', 'snap-mandatory');
                }
            });
            document.addEventListener('mousemove', (e) => {
                if (!isDragging) return;
                e.preventDefault();
                const x = e.pageX - track.offsetLeft;
                const walk = (x - startX) * 1.5;
                track.scrollLeft = scrollLeft - walk;
            });

            // Scroll with mouse wheel (Card by Card)
            let wheelTimeout;
            track.addEventListener('wheel', (e) => {
                if (e.deltaY !== 0) {
                    e.preventDefault();
                    if (wheelTimeout) return; // Prevent rapid triggers

                    const cardWidth = track.querySelector('[class*="snap-start"]')?.offsetWidth || 300;
                    track.scrollBy({ left: e.deltaY > 0 ? cardWidth : -cardWidth, behavior: 'smooth' });

                    wheelTimeout = setTimeout(() => {
                        wheelTimeout = null;
                    }, 400); // 400ms throttle delay matches typical smooth scroll
                }
            }, { passive: false });

            // Scroll progress bar + counter
            function updateProgress() {
                const maxScroll = track.scrollWidth - track.clientWidth;
                const pct = maxScroll > 0 ? (track.scrollLeft / maxScroll) * 100 : 0;
                progress.style.width = pct + '%';

                // Estimate current card index
                const cardWidth = track.scrollWidth / totalProducts;
                const current = Math.round(track.scrollLeft / cardWidth) + 1;
                const clamped = Math.min(Math.max(current, 1), totalProducts);
                counter.textContent = String(clamped).padStart(2, '0') + ' / ' + String(totalProducts).padStart(2, '0');
            }

            track.addEventListener('scroll', updateProgress, { passive: true });
            updateProgress();

            // Keyboard navigation when focused
            track.setAttribute('tabindex', '0');
            track.addEventListener('keydown', (e) => {
                const cardWidth = track.querySelector('[class*="snap-start"]')?.offsetWidth || 300;
                if (e.key === 'ArrowRight') { e.preventDefault(); track.scrollBy({ left: cardWidth, behavior: 'smooth' }); }
                if (e.key === 'ArrowLeft')  { e.preventDefault(); track.scrollBy({ left: -cardWidth, behavior: 'smooth' }); }
            });
        }

        // Carousel
        const inner = document.getElementById('carousel-inner');
        const prev = document.getElementById('prevBtn');
        const next = document.getElementById('nextBtn');
        let index = 0;
        const total = 2;
        
        function updateSlide() {
            inner.style.transform = `translateX(-${index * 100}%)`;
        }
        
        if(next && prev) {
            next.addEventListener('click', () => { index = (index + 1) % total; updateSlide(); });
            prev.addEventListener('click', () => { index = (index - 1 + total) % total; updateSlide(); });
            setInterval(() => { index = (index + 1) % total; updateSlide(); }, 5000);
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
<!-- Add to Cart Modal -->
    <div id="cartModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-opacity opacity-0">
        <div class="bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder w-full max-w-md p-6 relative transform scale-95 transition-transform duration-300">
            <button onclick="closeCartModal()" class="absolute top-4 right-4 text-lightMuted dark:text-darkMuted hover:text-lightMain dark:hover:text-darkMain">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <h2 class="display-font text-2xl mb-2" id="modalProductName">Product Name</h2>
            <p class="text-xs tracking-widest uppercase text-lightMuted dark:text-darkMuted mb-6" id="modalProductPrice">Rp0</p>
            
            <form id="cartModalForm" onsubmit="submitCartForm(event)" class="space-y-4">
                @csrf
                <input type="hidden" name="product_id" id="modalProductId">
                
                <div id="modalSizeContainer" class="hidden flex-col space-y-2">
                    <label class="text-[10px] tracking-widest uppercase font-bold">Ukuran</label>
                    <select name="size" id="modalSize" class="w-full bg-transparent border border-lightBorder dark:border-darkBorder p-3 text-sm focus:outline-none">
                        <option value="">Pilih Ukuran</option>
                    </select>
                </div>

                <div id="modalColorContainer" class="hidden flex-col space-y-2">
                    <label class="text-[10px] tracking-widest uppercase font-bold">Warna</label>
                    <select name="color" id="modalColor" class="w-full bg-transparent border border-lightBorder dark:border-darkBorder p-3 text-sm focus:outline-none">
                        <option value="">Pilih Warna</option>
                    </select>
                </div>
                
                <div class="flex flex-col space-y-2">
                    <label class="text-[10px] tracking-widest uppercase font-bold">Jumlah</label>
                    <input type="number" name="quantity" id="modalQuantity" value="1" min="1" class="w-full bg-transparent border border-lightBorder dark:border-darkBorder p-3 text-sm focus:outline-none">
                </div>

                <button type="submit" class="w-full bg-lightMain dark:bg-darkMain text-lightBg dark:text-darkBg py-4 text-[10px] tracking-[0.2em] uppercase font-bold mt-4 hover:opacity-90">
                    Konfirmasi Tambah
                </button>
            </form>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toastNotif" class="fixed bottom-6 right-6 z-[110] bg-lightMain dark:bg-darkMain text-lightBg dark:text-darkBg px-6 py-4 translate-y-24 opacity-0 transition-all duration-300 flex items-center space-x-3 shadow-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span class="text-xs font-bold tracking-widest uppercase">Berhasil ditambahkan ke keranjang!</span>
    </div>

    <script>
        const cartModal = document.getElementById('cartModal');
        const modalSizeContainer = document.getElementById('modalSizeContainer');
        const modalColorContainer = document.getElementById('modalColorContainer');
        const modalSize = document.getElementById('modalSize');
        const modalColor = document.getElementById('modalColor');

        function openCartModal(product) {
            document.getElementById('modalProductId').value = product.id;
            document.getElementById('modalProductName').innerText = product.name;
            document.getElementById('modalProductPrice').innerText = 'Rp' + parseInt(product.price).toLocaleString('id-ID');
            document.getElementById('modalQuantity').max = product.stock || 100;
            document.getElementById('modalQuantity').value = 1;

            // Handle Sizes
            if (product.sizes && product.sizes.length > 0) {
                modalSizeContainer.classList.remove('hidden');
                modalSizeContainer.classList.add('flex');
                modalSize.innerHTML = '<option value="">Pilih Ukuran (opsional)</option>';
                product.sizes.forEach(size => {
                    modalSize.innerHTML += `<option value="${size}">${size}</option>`;
                });
            } else {
                modalSizeContainer.classList.add('hidden');
                modalSizeContainer.classList.remove('flex');
                modalSize.innerHTML = '';
            }

            // Handle Colors
            if (product.colors && product.colors.length > 0) {
                modalColorContainer.classList.remove('hidden');
                modalColorContainer.classList.add('flex');
                modalColor.innerHTML = '<option value="">Pilih Warna (opsional)</option>';
                product.colors.forEach(color => {
                    modalColor.innerHTML += `<option value="${color}">${color}</option>`;
                });
            } else {
                modalColorContainer.classList.add('hidden');
                modalColorContainer.classList.remove('flex');
                modalColor.innerHTML = '';
            }

            cartModal.classList.remove('hidden');
            setTimeout(() => {
                cartModal.classList.remove('opacity-0');
                cartModal.firstElementChild.classList.remove('scale-95');
            }, 10);
        }

        function closeCartModal() {
            cartModal.classList.add('opacity-0');
            cartModal.firstElementChild.classList.add('scale-95');
            setTimeout(() => {
                cartModal.classList.add('hidden');
            }, 300);
        }

        function submitCartForm(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            fetch("{{ url('pelanggan/cart/add') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                closeCartModal();
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
    </script>

</body>
</html>














