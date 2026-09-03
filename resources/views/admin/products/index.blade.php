@extends('admin.layout.main')
@section('title', $title ?? 'Daftar Produk')
@section('content')

<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="display-font text-2xl">Daftar Produk</h2>
        <p class="text-sm text-lightMuted dark:text-darkMuted">Kelola katalog produk Anda di sini.</p>
    </div>
    
    @if (session('role') === 'superadmin' || optional(Auth::user())->role === 'superadmin')
        <a href="{{ url('/admin/products/create') }}" class="px-6 py-2 bg-black text-white dark:bg-white dark:text-black uppercase tracking-widest font-semibold text-xs rounded hover:opacity-80 transition-opacity">
            + Tambah Produk
        </a>
    @endif
</div>

<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b-minimal border-lightBorder dark:border-darkBorder">
                <th class="py-3 px-4 text-xs font-semibold uppercase tracking-widest">ID</th>
                <th class="py-3 px-4 text-xs font-semibold uppercase tracking-widest">Nama</th>
                <th class="py-3 px-4 text-xs font-semibold uppercase tracking-widest">Harga</th>
                <th class="py-3 px-4 text-xs font-semibold uppercase tracking-widest text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr class="border-b-minimal border-lightBorder dark:border-darkBorder hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                    <td class="py-4 px-4 text-sm">{{ $product->id ?? $product['id'] ?? '' }}</td>
                    <td class="py-4 px-4 text-sm font-semibold">{{ $product->name ?? $product['name'] ?? '' }}</td>
                    <td class="py-4 px-4 text-sm">Rp {{ number_format($product->price ?? $product['price'] ?? 0, 0, ',', '.') }}</td>
                    <td class="py-4 px-4 text-sm flex justify-end gap-3">
                        <a href="{{ url('/admin/products/edit/' . ($product->id ?? $product['id'] ?? '')) }}" class="text-blue-500 hover:underline uppercase text-[10px] font-semibold tracking-widest">Edit</a>
                        
                        @if (session('role') === 'superadmin' || optional(Auth::user())->role === 'superadmin')
                            <a href="{{ url('/admin/products/delete/' . ($product->id ?? $product['id'] ?? '')) }}" onclick="return confirm('Yakin ingin menghapus?')" class="text-red-500 hover:underline uppercase text-[10px] font-semibold tracking-widest">Hapus</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="py-8 text-center text-lightMuted dark:text-darkMuted text-sm">Belum ada produk.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
