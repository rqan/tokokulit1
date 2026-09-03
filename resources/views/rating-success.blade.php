<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rating Berhasil - TOKO RAFI</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind-config.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ux.css') }}">
    <script src="{{ asset('js/ux.js') }}"></script>
</head>
<body class="w-full min-h-screen relative antialiased selection:bg-lightMain selection:text-lightBg dark:selection:bg-darkMain dark:selection:text-darkBg bg-lightBg text-lightMain dark:bg-darkBg dark:text-darkMain flex items-center justify-center p-4">

    

    <div class="w-full max-w-md text-center p-8">
        
        <div class="w-24 h-24 mx-auto mb-8 rounded-full border-2 border-green-500 flex items-center justify-center text-green-500">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <h1 class="display-font text-4xl uppercase mb-6">Terima Kasih!</h1>
        <p class="text-sm tracking-widest leading-relaxed text-lightMuted dark:text-darkMuted mb-12">
            Penilaian Anda telah berhasil dikirim. Ulasan Anda sangat berarti bagi kami untuk terus meningkatkan kualitas produk dan layanan.
        </p>

        <a href="{{ url('/') }}" class="inline-block px-8 py-4 bg-lightMain dark:bg-darkMain text-lightBg dark:text-darkBg text-center text-[10px] tracking-[0.2em] uppercase font-bold hover:opacity-90 transition-opacity">
            Kembali ke Beranda
        </a>
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
</body>
</html>


