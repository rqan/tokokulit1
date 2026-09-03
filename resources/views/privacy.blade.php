<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Privacy Policy - {{ config('app.name', 'Antigravity') }}</title>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
        @endif
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] min-h-screen flex flex-col font-sans antialiased">
        
        <!-- Header / Nav -->
        <header class="w-full max-w-5xl mx-auto px-6 py-6 flex justify-between items-center border-b border-gray-200 dark:border-gray-800">
            <a href="{{ url('/') }}" class="font-bold text-xl tracking-tight hover:opacity-80 transition-opacity">
                {{ config('app.name', 'Antigravity') }}
            </a>
            <nav class="flex gap-4">
                <a href="{{ url('/') }}" class="text-sm font-medium hover:text-gray-500 transition-colors">Home</a>
                <a href="{{ url('/katalog') }}" class="text-sm font-medium hover:text-gray-500 transition-colors">Katalog</a>
            </nav>
        </header>

        <!-- Main Content -->
        <main class="flex-grow w-full max-w-3xl mx-auto px-6 py-12 lg:py-20">
            <div class="mb-10">
                <h1 class="text-3xl lg:text-4xl font-semibold mb-4 tracking-tight">Privacy Policy</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Last updated: {{ date('F d, Y') }}</p>
            </div>

            <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 space-y-6 leading-relaxed">
                <section>
                    <h2 class="text-xl font-semibold text-black dark:text-white mb-3">1. Informasi yang Kami Kumpulkan</h2>
                    <p>
                        Kami dapat mengumpulkan informasi pribadi yang Anda berikan secara sukarela saat menggunakan situs web kami, seperti nama, alamat email, dan informasi kontak lainnya saat Anda mendaftar atau berlangganan newsletter kami.
                    </p>
                </section>
                
                <section>
                    <h2 class="text-xl font-semibold text-black dark:text-white mb-3">2. Penggunaan Informasi</h2>
                    <p>
                        Informasi yang kami kumpulkan digunakan untuk:
                    </p>
                    <ul class="list-disc pl-5 mt-2 space-y-1">
                        <li>Menyediakan, mengoperasikan, dan memelihara situs web kami.</li>
                        <li>Meningkatkan, mempersonalisasi, dan memperluas pengalaman Anda.</li>
                        <li>Mengirimkan email berkala seperti newsletter, jika Anda memilih untuk berlangganan.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-black dark:text-white mb-3">3. Keamanan Data</h2>
                    <p>
                        Kami memprioritaskan keamanan data pribadi Anda dan menerapkan langkah-langkah keamanan teknis serta organisasi yang tepat untuk melindunginya dari akses, perubahan, atau pengungkapan yang tidak sah.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-black dark:text-white mb-3">4. Hubungi Kami</h2>
                    <p>
                        Jika Anda memiliki pertanyaan tentang Kebijakan Privasi ini, silakan hubungi kami melalui halaman kontak atau kirim email ke <a href="mailto:support@example.com" class="text-blue-600 hover:underline">support@example.com</a>.
                    </p>
                </section>
            </div>
            
            <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-800">
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-5 py-2.5 border border-gray-300 dark:border-gray-700 rounded-md text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                    Kembali ke Beranda
                </a>
            </div>
        </main>

        <!-- Footer -->
        <footer class="w-full max-w-5xl mx-auto px-6 py-8 border-t border-gray-200 dark:border-gray-800 text-sm text-center text-gray-500">
            &copy; {{ date('Y') }} {{ config('app.name', 'Antigravity') }}. All rights reserved.
                        <div class="w-full flex justify-end px-8 py-4 text-[9px] font-bold tracking-[0.2em] uppercase text-lightMuted dark:text-darkMuted">
            <a href="https://mein-profile.vercel.app" target="_blank" rel="noopener noreferrer" class="hover:text-lightMain dark:hover:text-darkMain transition-colors">&copy; Copyright REGANDEWA 2026</a>
        </div>
</footer>
    </body>
</html>


