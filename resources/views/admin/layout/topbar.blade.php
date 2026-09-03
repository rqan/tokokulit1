<header class="w-full px-8 py-6 flex justify-between items-center border-b-minimal border-lightBorder dark:border-darkBorder sticky top-0 z-40 bg-lightBg dark:bg-darkBg transition-colors">
    <div class="display-font text-3xl tracking-tight">
        @yield('title', 'DASHBOARD')
    </div>
    
    <div class="flex items-center gap-6">
        <div class="text-[10px] font-semibold tracking-[0.2em] uppercase">
            Welcome, {{ session('username') ?? optional(Auth::user())->username ?? 'Admin' }}!
            <span class="ml-2 px-2 py-1 bg-black/10 dark:bg-white/10 rounded">
                {{ strtoupper(session('role') ?? optional(Auth::user())->role ?? 'ADMIN') }}
            </span>
        </div>
        
        <button id="themeToggle" class="text-[10px] font-semibold tracking-[0.2em] uppercase hover:opacity-70 transition-colors focus:outline-none">
            Theme
        </button>
        <a href="{{ url('/logout') }}" class="text-[10px] font-semibold tracking-[0.2em] uppercase hover:text-red-500 transition-colors">
            Logout
        </a>
    </div>
</header>
