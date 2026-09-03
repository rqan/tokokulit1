<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - ENY LEATHER</title>
    
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
        <div class="max-w-4xl mx-auto">
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

            <h1 class="display-font text-5xl md:text-7xl mb-12 text-center">Contact Us</h1>

            <!-- Fraud Warning -->
            <div class="bg-gray-100 dark:bg-gray-900 p-8 rounded-lg mb-12 border border-gray-200 dark:border-gray-800">
                <h3 class="text-xl font-bold mb-4">Lindungi diri dari penipuan</h3>
                <p class="mb-4">Berikut hal-hal yang dapat Anda lakukan saat menerima pesan dari siapapun yang mengatasnamakan ENY LEATHER:</p>
                <p class="mb-4">ENY LEATHER telah mengetahui adanya pesan tidak resmi melalui Facebook / WhatsApp / SMS / Email yang menawarkan penghasilan tambahan atau peluang kerja yang mengatasnamakan ENY LEATHER.</p>
                <p class="text-red-500 font-semibold mb-4">Kami ingin mengingatkan seluruh pelanggan bahwa pesan tidak resmi tersebut bukan dari ENY LEATHER atau afiliasi kami, dan kami meminta pelanggan ENY LEATHER untuk waspada terhadap penipuan.</p>
                <p>Kami pastikan bahwa tidak ada data pribadi pelanggan ENY LEATHER yang tersebar. ENY LEATHER sangat mengutamakan perlindungan data pribadi pelanggan. Pemberitahuan ini kami buat untuk memastikan bahwa seluruh pelanggan kami mengetahui adanya insiden penipuan ini dan untuk senantiasa waspada.</p>
            </div>

            <!-- Contact Options -->
            <div class="text-center mb-8">
                <p class="text-lg font-medium">Atau silakan menghubungi kami melalui layanan berikut:</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Email -->
                <div class="border border-lightBorder dark:border-darkBorder p-8 rounded-xl flex flex-col items-center justify-center text-center hover:shadow-md transition-shadow">
                    <div class="w-16 h-16 mb-4 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                    </div>
                    <h4 class="font-bold text-lg mb-2">E-mail</h4>
                    <p class="text-sm mb-1">customer@id.enyleather.com</p>
                    <p class="text-xs text-lightMuted dark:text-darkMuted mb-6">Senin - Minggu 09.00 - 18.00 WIB<br>*Kecuali Hari Libur Nasional</p>
                    <a href="https://mail.google.com/mail/?view=cm&fs=1&to=customer@id.enyleather.com" target="_blank" class="px-6 py-2 border border-lightMain dark:border-darkMain text-xs font-bold uppercase tracking-wider hover:bg-lightMain hover:text-lightBg dark:hover:bg-darkMain dark:hover:text-darkBg transition-colors">Email Kami ></a>
                </div>

                <!-- Chat -->
                <div class="border border-lightBorder dark:border-darkBorder p-8 rounded-xl flex flex-col items-center justify-center text-center hover:shadow-md transition-shadow">
                    <div class="w-16 h-16 mb-4 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 text-green-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-8 h-8">
                            <path d="M12.031 21.055a8.913 8.913 0 0 1-4.57-1.258l-.328-.195-3.398.89 1.05-3.328-.214-.34a8.927 8.927 0 0 1-1.375-4.781c0-4.942 4.027-8.969 8.97-8.969 2.395 0 4.644.933 6.335 2.628A8.932 8.932 0 0 1 21 12.043c0 4.945-4.027 8.973-8.969 8.973zm-4.73-2.184h.012l.004.004zM2.85 22.184l1.52-4.813A10.457 10.457 0 0 1 3.075 12.04c0-5.789 4.71-10.496 10.492-10.496 2.805 0 5.438 1.094 7.422 3.078A10.435 10.435 0 0 1 24 12.047c0 5.793-4.71 10.504-10.496 10.504a10.444 10.444 0 0 1-5.32-1.453L2.85 22.184zm14.398-7.398c-.168-.285-.614-.457-1.285-.793-.672-.336-3.973-1.961-4.586-2.184-.613-.226-1.062-.336-1.508.336-.445.672-1.734 2.184-2.125 2.633-.39.445-.781.504-1.453.168-.672-.336-2.836-1.047-5.402-3.344-1.996-1.785-3.344-3.988-3.734-4.66-.39-.672-.043-1.035.293-1.371.293-.293.672-.781 1.008-1.172.336-.39.449-.672.672-1.121.222-.445.11-.84-.055-1.176-.168-.336-1.508-3.64-2.066-4.984-.543-1.313-1.094-1.137-1.508-1.156-.39-.016-.836-.02-1.285-.02-.445 0-1.172.168-1.785.84C3.895 3.594 1.83 5.504 1.83 9.3c0 3.797 2.066 7.469 2.355 7.859.289.39 5.371 8.203 13 11.5 1.816.785 3.234 1.258 4.336 1.61 1.824.578 3.484.496 4.793.3 1.465-.219 4.516-1.844 5.156-3.629.64-1.785.64-3.312.449-3.629z"/>
                        </svg>
                    </div>
                    <h4 class="font-bold text-lg mb-2">WhatsApp</h4>
                    <p class="text-xs text-lightMuted dark:text-darkMuted mb-6 mt-6">Senin - Minggu 09.00 - 18.00 WIB<br>*Kecuali Hari Libur Nasional</p>
                    <a href="https://wa.me/089249912345" target="_blank" class="px-6 py-2 border border-lightMain dark:border-darkMain text-xs font-bold uppercase tracking-wider hover:bg-lightMain hover:text-lightBg dark:hover:bg-darkMain dark:hover:text-darkBg transition-colors mt-auto">Chat Dengan Kami ></a>
                </div>

                <!-- Telephone -->
                <div class="border border-lightBorder dark:border-darkBorder p-8 rounded-xl flex flex-col items-center justify-center text-center hover:shadow-md transition-shadow">
                    <div class="w-16 h-16 mb-4 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.896-1.596-5.48-4.18-7.076-7.076l1.293-.97c.362-.271.527-.733.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                        </svg>
                    </div>
                    <h4 class="font-bold text-lg mb-2">Telephone</h4>
                    <p class="text-sm mb-1">021-29490100</p>
                    <p class="text-xs text-lightMuted dark:text-darkMuted">Senin - Minggu 24/7</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full pt-16 border-t-minimal border-lightBorder dark:border-darkBorder">
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
            <h1 class="display-font text-[20vw] leading-none text-lightMain dark:text-darkMain select-none">ENY LEATHER</h1>
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



