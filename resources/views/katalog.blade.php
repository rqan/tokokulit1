<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog - ENY LEATHER</title>
    
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
            <a href="{{ url('/') }}" class="display-font text-4xl tracking-tight">ENY LEATHER&reg;</a>
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

    <div class="flex flex-col md:flex-row min-h-screen px-8 py-8 gap-12">
        <!-- Sidebar -->
        <aside class="w-full md:w-48 flex-shrink-0">
            <!-- Search -->
            <div class="mb-8 relative">
                <form action="{{ url('/katalog') }}" method="GET">
                    <input type="text" name="q" id="searchInput" value="{{ $q }}" placeholder="Search products..." class="w-full bg-transparent border-b border-lightBorder dark:border-darkBorder pb-2 text-[10px] uppercase tracking-widest focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors">
                    @if($selectedCategory) <input type="hidden" name="category" value="{{ $selectedCategory }}"> @endif
                    @if($gender) <input type="hidden" name="gender" value="{{ $gender }}"> @endif
                </form>
            </div>
            
            <!-- Categories -->
            <div>
                <h3 class="text-[10px] tracking-[0.2em] uppercase font-bold mb-4 text-lightMuted dark:text-darkMuted">Categories</h3>
                <ul class="space-y-3 text-[10px] tracking-widest uppercase">
                    @foreach($categories as $cat)
                    <li>
                        <a href="{{ url('/katalog?category='.$cat . ($q ? '&q='.$q : '') . ($gender ? '&gender='.$gender : '')) }}" class="{{ $selectedCategory == $cat ? 'text-lightMain dark:text-darkMain border-b border-lightMain dark:border-darkMain pb-1' : 'hover:text-lightMain dark:hover:text-darkMain' }}">
                            {{ $cat }}
                        </a>
                    </li>
                    @endforeach
                    @if($selectedCategory)
                    <li class="pt-2">
                        <a href="{{ url('/katalog?' . ($q ? 'q='.$q : '') . ($gender ? '&gender='.$gender : '')) }}" class="text-red-500 hover:text-red-600 border-b border-transparent hover:border-red-600 pb-1 inline-block">Clear Category</a>
                    </li>
                    @endif
                </ul>
            </div>
            
        </aside>

        <!-- Main Product Grid -->
        <main class="flex-1">
            <div class="mb-6 flex justify-between items-center pb-4 border-b-minimal border-lightBorder dark:border-darkBorder">
                <h1 class="display-font text-3xl">
                    @if($q) Search results for "{{ $q }}"
                    @elseif($selectedCategory) {{ $selectedCategory }}
                    @elseif($gender) {{ $gender }}'s Collection
                    @else All Products
                    @endif
                </h1>
                <span class="text-[10px] tracking-widest uppercase text-lightMuted dark:text-darkMuted">{{ count($products) }} Items Found</span>
            </div>

            @if(count($products) > 0)
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    @foreach ($products as $product)
                        <div class="group flex flex-col cursor-pointer border border-transparent hover:border-lightBorder dark:hover:border-darkBorder transition-all pb-4">
                            <!-- Image -->
                        <div class="w-full aspect-[3/2] bg-[#E5E5E5] dark:bg-[#1E1E1E] overflow-hidden flex items-center justify-center p-10 transition-colors relative"
                            @if(session('isLoggedIn') || Auth::check())
                                onclick="event.preventDefault(); event.stopPropagation(); openCartModal({{ htmlspecialchars(json_encode($product)) }});"
                            @else
                                onclick="event.preventDefault(); event.stopPropagation(); window.location.href='{{ url('/login') }}'"
                            @endif
                        >
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
                        <a href="{{ url('/product/' . $product['id']) }}" class="block">
                            <div class="pt-4 flex flex-col justify-between">
                                <h3 class="display-font text-lg mb-1 text-lightMain dark:text-darkMain truncate">{{ $product['name'] }}</h3>
                                <p class="text-[9px] tracking-widest uppercase text-lightMuted dark:text-darkMuted mb-2 truncate">{{ $product['category'] ?? $product['description'] }}</p>
                                <div class="display-font text-md text-lightMain dark:text-darkMain font-semibold">
                                    Rp{{ number_format($product['price'], 0, ',', '.') }}
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="w-full text-center py-32 text-lightMuted dark:text-darkMuted display-font text-3xl">
                    NO PRODUCTS FOUND.
                </div>
            @endif
        </main>
    </div>

    <footer class="w-full pt-16 mt-auto">
        <div class="w-full overflow-hidden text-center pb-2 border-t-minimal border-lightBorder dark:border-darkBorder pt-8">
            <h1 class="display-font text-[10vw] leading-none text-lightMain dark:text-darkMain select-none">ENY LEATHER</h1>
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
                if (html.classList.contains('dark')) {
                    localStorage.theme = 'dark';
                } else {
                    localStorage.theme = 'light';
                }
            });
        }
        
        // Focus search input if focus_search is set
        @if(request('focus_search'))
            window.onload = function() {
                const searchInput = document.getElementById('searchInput');
                if(searchInput) {
                    searchInput.focus();
                }
            }
        @endif
    
        // Live Search AJAX
        const searchInput = document.getElementById('searchInput');
        const searchForm = searchInput ? searchInput.closest('form') : null;
        
        if (searchInput && searchForm) {
            let debounceTimer;
            
            const performSearch = () => {
                const url = new URL(searchForm.action);
                const formData = new FormData(searchForm);
                
                for (const [key, value] of formData.entries()) {
                    if(value) url.searchParams.append(key, value);
                }
                
                fetch(url)
                    .then(res => res.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        const currentMain = document.querySelector('main');
                        const newMain = doc.querySelector('main');
                        
                        if(currentMain && newMain) {
                            currentMain.innerHTML = newMain.innerHTML;
                        }
                        
                        // Update URL without reloading to allow sharing links
                        window.history.replaceState({}, '', url);
                    });
            };

            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(performSearch, 300);
            });
            
                        searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                clearTimeout(debounceTimer);
                performSearch();
            });
        }
        
        // Intercept Sidebar Links for AJAX
        document.addEventListener('click', function(e) {
            const link = e.target.closest('aside a');
            if (link) {
                e.preventDefault();
                const url = link.href;
                fetch(url)
                    .then(res => res.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        const currentMain = document.querySelector('main');
                        const newMain = doc.querySelector('main');
                        if(currentMain && newMain) {
                            currentMain.innerHTML = newMain.innerHTML;
                        }
                        
                        const currentAside = document.querySelector('aside');
                        const newAside = doc.querySelector('aside');
                        if(currentAside && newAside) {
                            currentAside.innerHTML = newAside.innerHTML;
                        }
                        
                        window.history.pushState({}, '', url);
                    });
            }
        });

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
























