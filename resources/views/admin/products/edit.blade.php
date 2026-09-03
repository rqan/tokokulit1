@extends('admin.layout.main')
@section('title', $title ?? 'Edit Produk')
@section('content')

<div class="mb-6 flex justify-between items-end">
    <div>
        <h2 class="display-font text-3xl">EDIT PRODUCT</h2>
        <p class="text-sm text-lightMuted dark:text-darkMuted mt-1">Update product details and inventory.</p>
    </div>
</div>

<form action="{{ url('/admin/products/update/' . ($product->id ?? $product['id'] ?? '')) }}" method="post" enctype="multipart/form-data" onsubmit="return validateImages()" class="w-full max-w-4xl bg-black/5 dark:bg-white/5 p-8 rounded-lg border border-lightBorder dark:border-darkBorder shadow-sm">
    @csrf
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <!-- Basic Info -->
        <div class="space-y-6">
            <div>
                <label class="block text-[10px] uppercase tracking-widest font-bold mb-3 text-lightMuted dark:text-darkMuted">Nama Produk</label>
                <input type="text" name="name" value="{{ $product->name ?? $product['name'] ?? '' }}" required class="w-full px-4 py-3 bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm">
            </div>
            
            <div>
                <label class="block text-[10px] uppercase tracking-widest font-bold mb-3 text-lightMuted dark:text-darkMuted">Harga (Rp)</label>
                <input type="number" name="price" value="{{ $product->price ?? $product['price'] ?? 0 }}" required class="w-full px-4 py-3 bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm">
            </div>

            <div>
                <label class="block text-[10px] uppercase tracking-widest font-bold mb-3 text-lightMuted dark:text-darkMuted">Kategori</label>
                <input type="text" name="category" value="{{ optional($product->category)->name }}" class="w-full px-4 py-3 bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm">
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <div>
                <label class="block text-[10px] uppercase tracking-widest font-bold mb-3 text-lightMuted dark:text-darkMuted">Gender</label>
                <select name="gender" class="w-full px-4 py-3 bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm">
                    <option value="" {{ empty($product->gender) ? 'selected' : '' }}>Pilih Gender (Opsional)</option>
                    <option value="pria" {{ strtolower($product->gender) == 'pria' ? 'selected' : '' }}>Pria</option>
                    <option value="wanita" {{ strtolower($product->gender) == 'wanita' ? 'selected' : '' }}>Wanita</option>
                    <option value="unisex" {{ strtolower($product->gender) == 'unisex' ? 'selected' : '' }}>Unisex</option>
                </select>
            </div>

            <!-- Details -->
            <div>
                <label class="block text-[10px] uppercase tracking-widest font-bold mb-3 text-lightMuted dark:text-darkMuted">Deskripsi</label>
                <textarea name="description" rows="5" class="w-full px-4 py-3 bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm resize-none">{{ $product->description ?? $product['description'] ?? '' }}</textarea>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 pt-6 border-t border-lightBorder dark:border-darkBorder">
        <div>
            <label class="block text-[10px] uppercase tracking-widest font-bold mb-3 text-lightMuted dark:text-darkMuted">Stok</label>
            <input type="number" name="stock" value="{{ $product->stock }}" class="w-full px-4 py-3 bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm">
        </div>
        
        <div>
            <label class="block text-[10px] uppercase tracking-widest font-bold mb-3 text-lightMuted dark:text-darkMuted">Ukuran (Dipisah Koma)</label>
            <input type="text" name="sizes" value="{{ is_array($product->sizes) ? implode(', ', $product->sizes) : '' }}" placeholder="Misal: S, M, L, atau 40, 41" class="w-full px-4 py-3 bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm">
        </div>
        
        <div>
            <label class="block text-[10px] uppercase tracking-widest font-bold mb-3 text-lightMuted dark:text-darkMuted">Warna (Dipisah Koma)</label>
            <input type="text" name="colors" value="{{ is_array($product->colors) ? implode(', ', $product->colors) : '' }}" placeholder="Misal: Hitam, Putih, Merah" class="w-full px-4 py-3 bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm">
        </div>
    </div>

    <!-- Luxury Attributes -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10 pt-6 border-t border-lightBorder dark:border-darkBorder">
        <div>
            <label class="block text-[10px] uppercase tracking-widest font-bold mb-3 text-lightMuted dark:text-darkMuted">Availability Status</label>
            <select name="availability_status" class="w-full px-4 py-3 bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm">
                <option value="Available" {{ $product->availability_status == 'Available' ? 'selected' : '' }}>Available</option>
                <option value="Waitlist" {{ $product->availability_status == 'Waitlist' ? 'selected' : '' }}>Waitlist</option>
                <option value="Discontinued" {{ $product->availability_status == 'Discontinued' ? 'selected' : '' }}>Discontinued</option>
            </select>
        </div>
        <div>
            <label class="block text-[10px] uppercase tracking-widest font-bold mb-3 text-lightMuted dark:text-darkMuted">Produksi</label>
            <input type="text" name="provenance" value="{{ $product->provenance ?? '' }}" placeholder="e.g. Swiss Made, COSC Certified" class="w-full px-4 py-3 bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm">
        </div>
    </div>

        <div class="mb-8 pt-6 border-t border-lightBorder dark:border-darkBorder">
        <label class="block text-[10px] uppercase tracking-widest font-bold mb-3 text-lightMuted dark:text-darkMuted">Perbarui Foto Produk (Pilih minimal 2 foto baru jika ingin mengganti)</label>
        <input type="file" name="images[]" multiple accept="image/*" class="w-full px-4 py-3 bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm" id="imageInput">
        <p class="text-xs text-red-500 mt-2 hidden" id="imageError">Minimal harus mengunggah 2 gambar jika Anda ingin mengganti foto produk!</p>
    </div>
    <div class="flex items-center justify-end gap-4 pt-6 border-t border-lightBorder dark:border-darkBorder">
        <a href="{{ url('/admin/products') }}" class="px-8 py-3 text-[10px] uppercase tracking-[0.2em] font-bold text-lightMuted hover:text-lightMain dark:text-darkMuted dark:hover:text-darkMain transition-colors">Batal</a>
        <button type="submit" class="px-8 py-3 bg-lightMain dark:bg-darkMain text-lightBg dark:text-darkBg text-[10px] uppercase tracking-[0.2em] font-bold rounded-full hover:opacity-90 transition-opacity">Simpan Perubahan</button>
    </div>
</form>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    function validateImages() {
        const input = document.getElementById('imageInput');
        if (input && input.files.length > 0 && input.files.length < 2) {
            document.getElementById('imageError').classList.remove('hidden');
            return false;
        }
        return true;
    }
</script>
@endsection


