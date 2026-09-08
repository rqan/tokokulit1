<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog - TOKO RAFI</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind-config.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ux.css') }}">
    <script src="{{ asset('js/ux.js') }}"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- SEO Schema JSON-LD -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "ItemList",
      "itemListElement": [
        @foreach($products as $index => $product)
        {
          "@@type": "ListItem",
          "position": {{ $index + 1 }},
          "item": {
            "@@type": "Product",
            "url": "{{ url('/product/' . $product['id']) }}",
            "name": "{{ $product['name'] }}",
            "image": "{{ $product['image_url'] ?? '' }}",
            "offers": {
              "@@type": "Offer",
              "price": "{{ $product['price'] }}",
              "priceCurrency": "IDR"
            }
          }
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
      ]
    }
    </script>
</head>
<body class="w-full relative antialiased selection:bg-lightMain selection:text-lightBg dark:selection:bg-darkMain dark:selection:text-darkBg bg-lightBg text-lightMain dark:bg-darkBg dark:text-darkMain">

    <!-- Header -->
    <header class="w-full px-8 py-6 flex justify-between items-center border-b-minimal border-lightBorder dark:border-darkBorder sticky top-0 z-40 bg-lightBg dark:bg-darkBg transition-colors">
        <div class="flex-1 flex justify-start">
            <a href="{{ url('/') }}" class="display-font text-4xl tracking-tight">TOKO RAFI</a>
        </div>
        <div class="hidden md:flex flex-1 justify-center space-x-12 text-[10px] font-semibold tracking-[0.2em] uppercase">
            <a href="{{ url('/') }}" class="link-hover">Home</a>
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

    <div x-data="catalogApp({{ json_encode(request()->all()) }})" class="flex flex-col md:flex-row min-h-screen px-8 py-8 gap-12">
        <!-- Sidebar -->
        <aside class="w-full md:w-48 flex-shrink-0 md:sticky md:top-28 h-max" aria-label="Catalog Filters">
            <!-- Search -->
            <div class="mb-8 relative">
                <input type="text" x-model="filters.q" @input.debounce.500ms="fetchProducts()" id="searchInput" placeholder="Search products..." class="w-full bg-transparent border-b border-lightBorder dark:border-darkBorder pb-2 text-[10px] uppercase tracking-widest focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors">
            </div>
            
            <!-- Sort By -->
            <div class="mb-8">
                <h3 class="text-[10px] tracking-[0.2em] uppercase font-bold mb-4 text-lightMuted dark:text-darkMuted">Sort By</h3>
                <select x-model="filters.sort" @change="fetchProducts()" class="w-full bg-transparent border-b border-lightBorder dark:border-darkBorder pb-2 text-[10px] tracking-widest uppercase focus:outline-none cursor-pointer">
                    <option class="bg-lightBg dark:bg-darkBg" value="newest">Latest Arrivals</option>
                    <option class="bg-lightBg dark:bg-darkBg" value="price_asc">Price: Low to High</option>
                    <option class="bg-lightBg dark:bg-darkBg" value="price_desc">Price: High to Low</option>
                </select>
            </div>

            <!-- Categories -->
            <div>
                <h3 class="text-[10px] tracking-[0.2em] uppercase font-bold mb-4 text-lightMuted dark:text-darkMuted">Categories</h3>
                <ul class="space-y-3 text-[10px] tracking-widest uppercase">
                    <li>
                        <label class="flex items-center cursor-pointer hover:text-lightMain dark:hover:text-darkMain transition-colors">
                            <input type="radio" value="" x-model="filters.category" @change="fetchProducts()" class="hidden">
                            <span :class="filters.category === '' ? 'text-lightMain dark:text-darkMain border-b border-lightMain dark:border-darkMain pb-1' : ''">All Categories</span>
                        </label>
                    </li>
                    @foreach($categories as $cat)
                    <li>
                        <label class="flex items-center cursor-pointer hover:text-lightMain dark:hover:text-darkMain transition-colors">
                            <input type="radio" value="{{ $cat }}" x-model="filters.category" @change="fetchProducts()" class="hidden">
                            <span :class="filters.category === '{{ $cat }}' ? 'text-lightMain dark:text-darkMain border-b border-lightMain dark:border-darkMain pb-1' : ''">{{ $cat }}</span>
                        </label>
                    </li>
                    @endforeach
                </ul>
            </div>
        </aside>

        <!-- Main Product Grid -->
        <main class="flex-1" aria-label="Product Listing">
            <div class="mb-6 flex justify-between items-center pb-4 border-b-minimal border-lightBorder dark:border-darkBorder">
                <h1 class="display-font text-3xl">
                    Collection
                </h1>
                <div class="flex items-center gap-4 text-[10px] tracking-widest uppercase text-lightMuted dark:text-darkMuted">
                    <span x-show="isLoading" class="animate-pulse text-lightMain dark:text-darkMain">Loading...</span>
                    <span x-text="`${totalItems} Items Found`"></span>
                </div>
            </div>

            <!-- Empty State -->
            <div x-show="products.length === 0" class="w-full text-center py-32 text-lightMuted dark:text-darkMuted display-font text-3xl" style="display: none;">
                NO PRODUCTS FOUND.
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 lg:gap-8" x-show="products.length > 0">
                <template x-for="product in products" :key="product.id">
                    <div class="group flex flex-col border border-transparent hover:border-lightBorder dark:hover:border-darkBorder transition-all pb-4 relative">
                        <!-- Image -->
                        <div class="w-full aspect-[3/2] bg-[#E5E5E5] dark:bg-[#1E1E1E] overflow-hidden flex items-center justify-center transition-colors relative">
                            
                            <img x-show="product.image_url"
                                :src="product.image_url"
                                :alt="product.name"
                                draggable="false"
                                loading="lazy"
                                class="object-cover w-full h-full grayscale group-hover:grayscale-0 group-hover:scale-105 transition-all duration-500 pointer-events-none"
                            >
                            
                            <span x-show="!product.image_url" class="display-font text-2xl text-lightBorder dark:text-darkBorder">NO IMAGE</span>
                            
                            <!-- Quick View Overlay -->
                            <div class="absolute inset-0 bg-lightBg/50 dark:bg-darkBg/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-sm z-10 pointer-events-auto">
                                <button @click="openQuickView(product)" class="bg-lightMain dark:bg-darkMain text-lightBg dark:text-darkBg px-6 py-3 text-[10px] uppercase tracking-widest font-bold hover:opacity-90 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                                    Quick View
                                </button>
                            </div>
                        </div>

                        <!-- Info -->
                        <a :href="`/product/${product.id}`" class="pt-4 flex flex-col justify-between px-4 z-0">
                            <h3 class="display-font text-lg mb-1 text-lightMain dark:text-darkMain truncate" x-text="product.name"></h3>
                            <p class="text-[9px] tracking-widest uppercase text-lightMuted dark:text-darkMuted mb-2 truncate" x-text="product.category || product.description"></p>
                            <div class="display-font text-md text-lightMain dark:text-darkMain font-semibold" x-text="formatRupiah(product.price)"></div>
                        </a>
                    </div>
                </template>
            </div>
            
            <!-- Pagination -->
            <div x-html="paginationHtml" class="mt-12 flex justify-center w-full" @click="handlePaginationClick"></div>
        </main>
    </div>

    <footer class="w-full pt-16 mt-auto">
        <div class="w-full overflow-hidden text-center pb-2 border-t-minimal border-lightBorder dark:border-darkBorder pt-8">
            <h1 class="display-font text-[10vw] leading-none text-lightMain dark:text-darkMain select-none">TOKO RAFI</h1>
        </div>
        <div class="w-full flex justify-end px-8 py-4 text-[9px] font-bold tracking-[0.2em] uppercase text-lightMuted dark:text-darkMuted">
            <a href="https://mein-profile.vercel.app" target="_blank" rel="noopener noreferrer" class="hover:text-lightMain dark:hover:text-darkMain transition-colors">&copy; Copyright REGANDEWA 2026</a>
        </div>
    </footer>

    <!-- Theme & Global JS -->
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
        
        @if(request('focus_search'))
            window.onload = function() {
                const searchInput = document.getElementById('searchInput');
                if(searchInput) {
                    searchInput.focus();
                }
            }
        @endif
    </script>

    <!-- Alpine App Logic -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('catalogApp', (initialFilters) => ({
                products: @json($products->items()),
                paginationHtml: `{!! addslashes($products->links('vendor.pagination.tailwind')) !!}`,
                totalItems: {{ $products->total() }},
                isLoading: false,
                
                filters: {
                    q: initialFilters.q || '',
                    category: initialFilters.category || '',
                    sort: initialFilters.sort || 'newest',
                    gender: initialFilters.gender || ''
                },

                async fetchProducts(url = null) {
                    this.isLoading = true;
                    
                    const queryParams = new URLSearchParams();
                    for (const key in this.filters) {
                        if (this.filters[key]) {
                            queryParams.append(key, this.filters[key]);
                        }
                    }
                    
                    const fetchUrl = url || `/katalog?${queryParams.toString()}`;

                    try {
                        const response = await fetch(fetchUrl, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });
                        
                        const data = await response.json();
                        
                        this.products = data.products;
                        this.paginationHtml = data.links;
                        this.totalItems = data.total;

                        window.history.pushState({}, '', fetchUrl);
                    } catch (error) {
                        console.error('Error fetching catalog data:', error);
                    } finally {
                        this.isLoading = false;
                        if(url) window.scrollTo({top: 0, behavior: 'smooth'});
                    }
                },
                
                handlePaginationClick(e) {
                    const link = e.target.closest('a');
                    if(link) {
                        e.preventDefault();
                        this.fetchProducts(link.href);
                    }
                },

                openQuickView(product) {
                    if(typeof openCartModal === 'function') {
                        openCartModal(product);
                    }
                },

                formatRupiah(price) {
                    return 'Rp' + new Intl.NumberFormat('id-ID').format(price);
                }
            }));
        });
    </script>

    <!-- Add to Cart Modal (Vanilla JS logic kept as requested) -->
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
                        <option value="" class="bg-lightBg dark:bg-darkBg text-lightMain dark:text-darkMain">Pilih Ukuran</option>
                    </select>
                </div>

                <div id="modalColorContainer" class="hidden flex-col space-y-2">
                    <label class="text-[10px] tracking-widest uppercase font-bold">Warna</label>
                    <select name="color" id="modalColor" class="w-full bg-transparent border border-lightBorder dark:border-darkBorder p-3 text-sm focus:outline-none">
                        <option value="" class="bg-lightBg dark:bg-darkBg text-lightMain dark:text-darkMain">Pilih Warna</option>
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
    <div id="toastNotif" class="fixed bottom-6 right-6 z-[110] bg-lightMain dark:bg-darkMain text-lightBg dark:text-darkBg px-6 py-4 translate-y-24 opacity-0 transition-all duration-300 flex items-center space-x-3 shadow-lg pointer-events-none">
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

            if (product.sizes && product.sizes.length > 0) {
                modalSizeContainer.classList.remove('hidden');
                modalSizeContainer.classList.add('flex');
                modalSize.innerHTML = '<option value="" class="bg-lightBg dark:bg-darkBg text-lightMain dark:text-darkMain">Pilih Ukuran (opsional)</option>';
                product.sizes.forEach(size => {
                    modalSize.innerHTML += `<option value="${size}" class="bg-lightBg dark:bg-darkBg text-lightMain dark:text-darkMain">${size}</option>`;
                });
            } else {
                modalSizeContainer.classList.add('hidden');
                modalSizeContainer.classList.remove('flex');
                modalSize.innerHTML = '';
            }

            if (product.colors && product.colors.length > 0) {
                modalColorContainer.classList.remove('hidden');
                modalColorContainer.classList.add('flex');
                modalColor.innerHTML = '<option value="" class="bg-lightBg dark:bg-darkBg text-lightMain dark:text-darkMain">Pilih Warna (opsional)</option>';
                product.colors.forEach(color => {
                    modalColor.innerHTML += `<option value="${color}" class="bg-lightBg dark:bg-darkBg text-lightMain dark:text-darkMain">${color}</option>`;
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
                alert('Terjadi kesalahan saat menambah ke keranjang.');
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
