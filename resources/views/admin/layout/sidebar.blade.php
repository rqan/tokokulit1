@php 
    $role = session('role') ?? optional(Auth::user())->role ?? 'admin'; 
@endphp
<aside class="w-64 fixed top-0 left-0 h-screen border-r-minimal border-lightBorder dark:border-darkBorder bg-lightBg dark:bg-darkBg z-50 flex flex-col transition-colors">
    <div class="px-8 py-6 border-b-minimal border-lightBorder dark:border-darkBorder">
        <div class="display-font text-3xl tracking-tight">TOKO RAFI</div>
        <div class="text-[10px] font-semibold tracking-[0.2em] uppercase mt-1">Admin Panel</div>
    </div>

    <nav class="flex-1 overflow-y-auto py-6 px-4 flex flex-col gap-2">
        <a href="{{ url('/admin/dashboard') }}" class="px-4 py-2 text-sm uppercase tracking-widest font-semibold hover:bg-black/5 dark:hover:bg-white/5 rounded transition-colors {{ Request::is('admin/dashboard') ? 'text-blue-500' : '' }}">
            Dashboard
        </a>
        
        <a href="{{ url('/admin/orders') }}" class="px-4 py-2 text-sm uppercase tracking-widest font-semibold hover:bg-black/5 dark:hover:bg-white/5 rounded transition-colors {{ Request::is('admin/orders*') ? 'text-blue-500' : '' }}">
            Pesanan
        </a>
        
        @if ($role === 'superadmin')
        <a href="{{ url('/admin/users') }}" class="px-4 py-2 text-sm uppercase tracking-widest font-semibold hover:bg-black/5 dark:hover:bg-white/5 rounded transition-colors {{ Request::is('admin/users') ? 'text-blue-500' : '' }}">
            Manajemen Pengguna
        </a>
        @endif
        
        <a href="{{ url('/admin/products') }}" class="px-4 py-2 text-sm uppercase tracking-widest font-semibold hover:bg-black/5 dark:hover:bg-white/5 rounded transition-colors {{ Request::is('admin/products') ? 'text-blue-500' : '' }}">
            Produk
        </a>
        
        <a href="{{ url('/admin/reviews') }}" class="px-4 py-2 text-sm uppercase tracking-widest font-semibold hover:bg-black/5 dark:hover:bg-white/5 rounded transition-colors {{ Request::is('admin/reviews') ? 'text-blue-500' : '' }}">
            Penilaian
        </a>
        
        <a href="{{ url('/admin/newsletter') }}" class="px-4 py-2 text-sm uppercase tracking-widest font-semibold hover:bg-black/5 dark:hover:bg-white/5 rounded transition-colors {{ Request::is('admin/newsletter') ? 'text-blue-500' : '' }}">
            Berita
        </a>

        @if ($role === 'superadmin')
        <a href="{{ url('/admin/payment-methods') }}" class="px-4 py-2 text-sm uppercase tracking-widest font-semibold hover:bg-black/5 dark:hover:bg-white/5 rounded transition-colors {{ Request::is('admin/payment-methods*') ? 'text-blue-500' : '' }}">
            Metode Pembayaran
        </a>
        
        <a href="{{ url('/admin/sales') }}" class="px-4 py-2 text-sm uppercase tracking-widest font-semibold hover:bg-black/5 dark:hover:bg-white/5 rounded transition-colors {{ Request::is('admin/sales') ? 'text-blue-500' : '' }}">
            Analisis Penjualan
        </a>
        
        <a href="{{ url('/admin/cms') }}" class="px-4 py-2 text-sm uppercase tracking-widest font-semibold hover:bg-black/5 dark:hover:bg-white/5 rounded transition-colors {{ Request::is('admin/cms') ? 'text-blue-500' : '' }}">
            Manajemen Toko
        </a>
        @endif
    </nav>
</aside>

