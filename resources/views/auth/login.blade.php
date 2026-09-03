<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - ENY LEATHER</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind-config.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ux.css') }}">
    <script src="{{ asset('js/ux.js') }}"></script>
</head>
<body class="min-h-screen flex items-center justify-center p-6 md:p-10 bg-lightBg text-lightMain dark:bg-darkBg dark:text-darkMain">
    
    <!-- Top Right Nav -->
    <div class="fixed top-6 right-6 md:top-10 md:right-10 z-50">
        <a href="{{ url('/') }}" class="text-[10px] md:text-xs font-semibold tracking-[0.2em] uppercase text-lightMuted dark:text-darkMuted hover:text-lightMain dark:hover:text-darkMain transition-colors">Close</a>
    </div>

    <!-- Login Box -->
    <div class="w-full max-w-md border-all-minimal border-lightBorder dark:border-darkBorder p-10 bg-lightBg dark:bg-darkBg shadow-2xl relative">
        <h1 class="display-font text-5xl mb-2">SIGN IN</h1>
        <p class="text-xs font-semibold tracking-[0.1em] text-lightMuted dark:text-darkMuted uppercase mb-8">Access your account</p>
        
        @if($errors->any())
            <div class="text-xs font-bold text-red-500 mb-4 border border-red-500 p-2 uppercase">{{ $errors->first() }}</div>
        @endif
        
        @if(session('message'))
            <div class="text-xs font-bold text-green-500 mb-4 border border-green-500 p-2 uppercase">{{ session('message') }}</div>
        @endif

        <form action="{{ url('/login') }}" method="post" class="flex flex-col gap-6">
            @csrf
            <div class="flex flex-col">
                <label class="text-[10px] font-bold tracking-[0.2em] uppercase mb-2">Email</label>
                <input type="email" name="email" required class="bg-transparent border-b-minimal border-lightBorder dark:border-darkBorder py-2 outline-none focus:border-lightMain dark:focus:border-darkMain text-sm transition-colors">
            </div>
            
            <div class="flex flex-col">
                <label class="text-[10px] font-bold tracking-[0.2em] uppercase mb-2">Password</label>
                <input type="password" name="password" required class="bg-transparent border-b-minimal border-lightBorder dark:border-darkBorder py-2 outline-none focus:border-lightMain dark:focus:border-darkMain text-sm transition-colors">
            </div>

            <button type="submit" class="mt-4 border-all-minimal border-lightMain dark:border-darkMain py-4 font-bold tracking-[0.2em] uppercase text-xs hover:bg-lightMain hover:text-lightBg dark:hover:bg-darkMain dark:hover:text-darkBg transition-colors">
                Proceed
            </button>
        </form>

        <div class="mt-8 text-center text-[10px] font-bold tracking-widest uppercase">
            <a href="{{ url('/register') }}" class="text-lightMuted dark:text-darkMuted hover:text-lightMain dark:hover:text-darkMain transition-colors border-b border-transparent hover:border-current pb-1">Create an account</a>
        </div>
    </div>

    <!-- Theme script -->
    <script>
        const html = document.documentElement;
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }
    </script>
</body>
</html>
