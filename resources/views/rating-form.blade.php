<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berikan Rating - ENY LEATHER</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind-config.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ux.css') }}">
    <script src="{{ asset('js/ux.js') }}"></script>
</head>
<body class="w-full min-h-screen relative antialiased selection:bg-lightMain selection:text-lightBg dark:selection:bg-darkMain dark:selection:text-darkBg bg-lightBg text-lightMain dark:bg-darkBg dark:text-darkMain flex items-center justify-center p-4">

    

    <div class="w-full max-w-lg bg-white dark:bg-[#111] border border-lightBorder dark:border-darkBorder p-8 md:p-12 shadow-2xl">
        <div class="text-center mb-10">
            <h1 class="display-font text-4xl uppercase mb-2">Penilaian Pesanan</h1>
            <p class="text-[10px] tracking-widest uppercase text-lightMuted dark:text-darkMuted">
                INV: {{ $order->invoice_number }}
            </p>
        </div>

        <div class="mb-8 p-4 bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder">
            <h2 class="text-[10px] tracking-widest uppercase font-bold mb-3 border-b-minimal border-lightBorder dark:border-darkBorder pb-2">Produk yang Dibeli</h2>
            <ul class="space-y-2 text-sm">
                @foreach($order->items as $item)
                    <li class="flex justify-between items-center">
                        <span class="uppercase truncate pr-4">{{ $item->product_name }}</span>
                        <span class="text-lightMuted dark:text-darkMuted text-[10px]">x{{ $item->quantity }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <form action="{{ url('/rating/' . $order->rating_token) }}" method="POST" class="space-y-8">
            @csrf
            
            <div class="text-center">
                <label class="block text-[10px] tracking-[0.2em] uppercase font-bold mb-4">Rating Anda</label>
                <div class="flex justify-center gap-2 text-4xl text-gray-300 dark:text-gray-700 cursor-pointer" id="star-container">
                    <span class="star hover:text-yellow-400 transition-colors" data-value="1">★</span>
                    <span class="star hover:text-yellow-400 transition-colors" data-value="2">★</span>
                    <span class="star hover:text-yellow-400 transition-colors" data-value="3">★</span>
                    <span class="star hover:text-yellow-400 transition-colors" data-value="4">★</span>
                    <span class="star hover:text-yellow-400 transition-colors" data-value="5">★</span>
                </div>
                <input type="hidden" name="rating" id="rating-input" value="0" required>
                <p id="rating-error" class="text-red-500 text-xs mt-2 hidden">Silakan pilih rating (1-5 bintang).</p>
            </div>

            <div>
                <label class="block text-[10px] tracking-[0.2em] uppercase font-bold mb-2">Ulasan (Opsional)</label>
                <textarea name="review" rows="4" maxlength="1000" placeholder="Bagaimana pengalaman Anda dengan produk ini?" class="w-full bg-transparent border border-lightBorder dark:border-darkBorder p-4 outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm resize-none"></textarea>
                <p class="text-right text-[9px] text-lightMuted dark:text-darkMuted mt-1">Maks 1000 karakter</p>
            </div>

            <button type="submit" id="submit-btn" class="w-full py-4 bg-lightMain dark:bg-darkMain text-lightBg dark:text-darkBg text-center text-[10px] tracking-[0.2em] uppercase font-bold hover:opacity-90 transition-opacity">
                Kirim Penilaian
            </button>
        </form>
    </div>

    <script>
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('rating-input');
        const form = document.querySelector('form');
        const error = document.getElementById('rating-error');

        stars.forEach(star => {
            star.addEventListener('click', () => {
                const val = star.getAttribute('data-value');
                ratingInput.value = val;
                error.classList.add('hidden');
                
                stars.forEach(s => {
                    if(s.getAttribute('data-value') <= val) {
                        s.classList.add('text-yellow-500');
                        s.classList.remove('text-gray-300', 'dark:text-gray-700');
                    } else {
                        s.classList.remove('text-yellow-500');
                        s.classList.add('text-gray-300', 'dark:text-gray-700');
                    }
                });
            });

            // Hover effect
            star.addEventListener('mouseenter', () => {
                const val = star.getAttribute('data-value');
                stars.forEach(s => {
                    if(s.getAttribute('data-value') <= val) {
                        s.classList.add('text-yellow-400');
                    }
                });
            });

            star.addEventListener('mouseleave', () => {
                stars.forEach(s => {
                    s.classList.remove('text-yellow-400');
                });
            });
        });

        form.addEventListener('submit', (e) => {
            if(ratingInput.value === "0") {
                e.preventDefault();
                error.classList.remove('hidden');
            }
        });

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
