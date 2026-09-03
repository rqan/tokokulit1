@extends('admin.layout.main')
@section('title', $title ?? 'Tambah Produk')
@section('content')

<div class="mb-6 flex justify-between items-end">
    <div>
        <h2 class="display-font text-3xl">TAMBAH PRODUK</h2>
        <p class="text-sm text-lightMuted dark:text-darkMuted mt-1">Add a new product to the catalog.</p>
    </div>
</div>

<form action="{{ url('/admin/products/store') }}" method="post" enctype="multipart/form-data" onsubmit="return validateImages()" class="w-full max-w-4xl bg-black/5 dark:bg-white/5 p-8 rounded-lg border border-lightBorder dark:border-darkBorder shadow-sm">
    @csrf
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <!-- Basic Info -->
        <div class="space-y-6">
            <div>
                <label class="block text-[10px] uppercase tracking-widest font-bold mb-3 text-lightMuted dark:text-darkMuted">Nama Produk</label>
                <input type="text" name="name" required class="w-full px-4 py-3 bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm">
            </div>
            
            <div>
                <label class="block text-[10px] uppercase tracking-widest font-bold mb-3 text-lightMuted dark:text-darkMuted">Harga (Rp)</label>
                <input type="number" name="price" required class="w-full px-4 py-3 bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm">
            </div>

                        <div x-data="{ isNew: false }">
                <label class="block text-[10px] uppercase tracking-widest font-bold mb-3 text-lightMuted dark:text-darkMuted">Kategori</label>
                <div class="flex gap-2">
                    <select x-show="!isNew" name="category" class="w-full px-4 py-3 bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm">
                        @foreach(\App\Models\Category::pluck('name') as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                    <input x-show="isNew" type="text" name="new_category" placeholder="Nama Kategori Baru" class="w-full px-4 py-3 bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm" :disabled="!isNew">
                    <button type="button" @click="isNew = !isNew" class="px-4 border border-lightBorder dark:border-darkBorder rounded text-xs font-bold whitespace-nowrap bg-black/5 hover:bg-black/10 dark:bg-white/5 dark:hover:bg-white/10" x-text="isNew ? 'Batal' : '+ Baru'"></button>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <div>
                <label class="block text-[10px] uppercase tracking-widest font-bold mb-3 text-lightMuted dark:text-darkMuted">Gender</label>
                <select name="gender" class="w-full px-4 py-3 bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm">
                    <option value="">Pilih Gender (Opsional)</option>
                    <option value="pria">Pria</option>
                    <option value="wanita">Wanita</option>
                    <option value="unisex">Unisex</option>
                </select>
            </div>

            <!-- Details -->
            <div>
                <label class="block text-[10px] uppercase tracking-widest font-bold mb-3 text-lightMuted dark:text-darkMuted">Deskripsi</label>
                <textarea name="description" rows="5" class="w-full px-4 py-3 bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm resize-none"></textarea>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 pt-6 border-t border-lightBorder dark:border-darkBorder">
        <div>
            <label class="block text-[10px] uppercase tracking-widest font-bold mb-3 text-lightMuted dark:text-darkMuted">Stok</label>
            <input type="number" name="stock" placeholder="Misal: 10" class="w-full px-4 py-3 bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm">
        </div>
        
        <div>
            <label class="block text-[10px] uppercase tracking-widest font-bold mb-3 text-lightMuted dark:text-darkMuted">Ukuran (Dipisah Koma)</label>
            <input type="text" name="sizes" placeholder="Misal: S, M, L, atau 40, 41" class="w-full px-4 py-3 bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm">
        </div>
        
        <div>
            <label class="block text-[10px] uppercase tracking-widest font-bold mb-3 text-lightMuted dark:text-darkMuted">Warna (Dipisah Koma)</label>
            <input type="text" name="colors" placeholder="Misal: Hitam, Putih, Merah" class="w-full px-4 py-3 bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm">
        </div>
    </div>

    <!-- Luxury Attributes -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10 pt-6 border-t border-lightBorder dark:border-darkBorder">
        <div>
            <label class="block text-[10px] uppercase tracking-widest font-bold mb-3 text-lightMuted dark:text-darkMuted">Availability Status</label>
            <select name="availability_status" class="w-full px-4 py-3 bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm">
                <option value="Available">Available</option>
                <option value="Waitlist">Waitlist</option>
                <option value="Discontinued">Discontinued</option>
            </select>
        </div>
        <div>
            <label class="block text-[10px] uppercase tracking-widest font-bold mb-3 text-lightMuted dark:text-darkMuted">Produksi</label>
            <input type="text" name="provenance" placeholder="e.g. Swiss Made, COSC Certified" class="w-full px-4 py-3 bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm">
        </div>
    </div>

        <div class="mb-8 pt-6 border-t border-lightBorder dark:border-darkBorder">
        <label class="block text-[10px] uppercase tracking-widest font-bold mb-3 text-lightMuted dark:text-darkMuted">Foto Produk (Minimal 2)</label>
        <input type="file" name="images[]" multiple accept="image/*" class="w-full px-4 py-3 bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm" id="imageInput">
        <p class="text-xs text-red-500 mt-2 hidden" id="imageError">Minimal harus mengunggah 2 gambar!</p>
    </div>
    <div class="flex items-center justify-end gap-4 pt-6 border-t border-lightBorder dark:border-darkBorder">
        <a href="{{ url('/admin/products') }}" class="px-8 py-3 text-[10px] uppercase tracking-[0.2em] font-bold text-lightMuted hover:text-lightMain dark:text-darkMuted dark:hover:text-darkMain transition-colors">Batal</a>
        <button type="submit" class="px-8 py-3 bg-lightMain dark:bg-darkMain text-lightBg dark:text-darkBg text-[10px] uppercase tracking-[0.2em] font-bold rounded-full hover:opacity-90 transition-opacity">Simpan</button>
    </div>
</form>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    function validateImages() {
        const input = document.getElementById('imageInput');
        if (input && input.files.length < 2) {
            document.getElementById('imageError').classList.remove('hidden');
            return false;
        }
        return true;
    }
</script>
@endsection


