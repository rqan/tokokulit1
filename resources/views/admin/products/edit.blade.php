@extends('admin.layout.main')
@section('title', $title ?? 'Edit Produk')
@section('content')

<div class="mb-6">
    <h2 class="display-font text-2xl uppercase">Edit Produk: {{ $product->name }}</h2>
    <p class="text-sm text-lightMuted dark:text-darkMuted">Formulir modern berbasis Alpine.js untuk edit data.</p>
</div>

<form action="{{ url('/admin/products/update/' . $product->id) }}" method="post" enctype="multipart/form-data" 
      class="space-y-6 bg-lightBg dark:bg-darkBg p-6 rounded-xl border border-lightBorder dark:border-darkBorder shadow-sm text-lightMain dark:text-darkMain"
      x-data="productEditForm({{ $product->stock ?? 0 }}, {{ json_encode($product->variants ?? []) }})">
    @csrf
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-xs font-bold tracking-[0.2em] uppercase text-lightMuted dark:text-darkMuted mb-2">Nama Produk</label>
            <input type="text" name="name" value="{{ $product->name }}" required class="mt-1 block w-full bg-transparent border border-lightBorder dark:border-darkBorder rounded p-2 outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors">
        </div>
        
        <div>
            <label class="block text-xs font-bold tracking-[0.2em] uppercase text-lightMuted dark:text-darkMuted mb-2">Harga (Rp)</label>
            <input type="number" name="price" value="{{ $product->price }}" required class="mt-1 block w-full bg-transparent border border-lightBorder dark:border-darkBorder rounded p-2 outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors">
        </div>

        <div>
            <label class="block text-xs font-bold tracking-[0.2em] uppercase text-lightMuted dark:text-darkMuted mb-2">Stok Utama (Total)</label>
            <input type="number" name="stock" x-model="totalStock" :readonly="variants.length > 0"
                   :class="variants.length > 0 ? 'bg-black/5 dark:bg-white/5 cursor-not-allowed text-lightMuted dark:text-darkMuted' : 'bg-transparent focus:border-lightMain dark:focus:border-darkMain'"
                   class="mt-1 block w-full border border-lightBorder dark:border-darkBorder rounded p-2 outline-none transition-colors">
            <p class="text-[10px] text-lightMuted dark:text-darkMuted mt-1">*Jika varian ditambahkan, stok utama dihitung otomatis.</p>
        </div>

        <div>
            <label class="block text-xs font-bold tracking-[0.2em] uppercase text-lightMuted dark:text-darkMuted mb-2">Kategori</label>
            <div class="flex gap-2" x-data="{ isNew: false }">
                <template x-if="!isNew">
                    <select name="category" class="block w-full bg-transparent border border-lightBorder dark:border-darkBorder rounded p-2 outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors">
                        <option value="" class="bg-lightBg dark:bg-darkBg">Pilih Kategori...</option>
                        @foreach(\App\Models\Category::all() as $cat)
                            <option value="{{ $cat->name }}" {{ optional($product->category)->name == $cat->name ? 'selected' : '' }} class="bg-lightBg dark:bg-darkBg">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </template>
                <template x-if="isNew">
                    <input type="text" name="new_category" placeholder="Nama kategori baru" class="block w-full bg-transparent border border-lightBorder dark:border-darkBorder rounded p-2 outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors">
                </template>
                <button type="button" @click="isNew = !isNew" class="border border-lightBorder dark:border-darkBorder bg-black/5 dark:bg-white/5 hover:bg-black/10 dark:hover:bg-white/10 px-3 py-2 rounded text-xs font-bold uppercase tracking-widest whitespace-nowrap transition-colors">
                    <span x-text="isNew ? 'Pilih Kategori' : '+ Baru'"></span>
                </button>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold tracking-[0.2em] uppercase text-lightMuted dark:text-darkMuted mb-2">Gender</label>
            <select name="gender" class="block w-full bg-transparent border border-lightBorder dark:border-darkBorder rounded p-2 outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors">
                <option value="Unisex" {{ strtolower($product->gender) == 'unisex' ? 'selected' : '' }} class="bg-lightBg dark:bg-darkBg">Unisex</option>
                <option value="Pria" {{ strtolower($product->gender) == 'pria' ? 'selected' : '' }} class="bg-lightBg dark:bg-darkBg">Pria</option>
                <option value="Wanita" {{ strtolower($product->gender) == 'wanita' ? 'selected' : '' }} class="bg-lightBg dark:bg-darkBg">Wanita</option>
            </select>
        </div>
        
        <div>
            <label class="block text-xs font-bold tracking-[0.2em] uppercase text-lightMuted dark:text-darkMuted mb-2">Status Produk</label>
            <select name="availability_status" class="block w-full bg-transparent border border-lightBorder dark:border-darkBorder rounded p-2 outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors">
                <option value="Available" {{ $product->availability_status == 'Available' ? 'selected' : '' }} class="bg-lightBg dark:bg-darkBg">Available</option>
                <option value="Waitlist" {{ $product->availability_status == 'Waitlist' ? 'selected' : '' }} class="bg-lightBg dark:bg-darkBg">Waitlist</option>
                <option value="Discontinued" {{ $product->availability_status == 'Discontinued' ? 'selected' : '' }} class="bg-lightBg dark:bg-darkBg">Discontinued</option>
            </select>
        </div>
    </div>

    <div class="mt-4">
        <label class="block text-xs font-bold tracking-[0.2em] uppercase text-lightMuted dark:text-darkMuted mb-2">Deskripsi Lengkap</label>
        <textarea name="description" rows="4" class="mt-1 block w-full bg-transparent border border-lightBorder dark:border-darkBorder rounded p-2 outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors">{{ $product->description }}</textarea>
    </div>

    <!-- VARIAN PRODUK (ALPINE JS) -->
    <div class="border border-lightBorder dark:border-darkBorder p-6 rounded-lg bg-black/5 dark:bg-white/5 mt-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-xs uppercase tracking-[0.2em] text-lightMain dark:text-darkMain">Varian Produk (Ukuran & Warna)</h3>
            <button type="button" @click="addVariant" class="text-[10px] bg-lightMain dark:bg-darkMain text-lightBg dark:text-darkBg px-3 py-1.5 rounded font-bold tracking-widest uppercase hover:opacity-90">
                + Tambah Varian
            </button>
        </div>

        <template x-if="variants.length === 0">
            <p class="text-xs text-lightMuted dark:text-darkMuted">Belum ada varian. Anda dapat menambahkan spesifikasi ukuran dan warna.</p>
        </template>

        <div class="space-y-4">
            <template x-for="(variant, index) in variants" :key="index">
                <div class="flex flex-wrap md:flex-nowrap gap-4 items-end border-b border-lightBorder dark:border-darkBorder pb-4">
                    <div class="flex-1">
                        <label class="block text-[10px] font-bold tracking-widest uppercase text-lightMuted dark:text-darkMuted mb-2">Ukuran (Opsional)</label>
                        <input type="text" x-model="variant.size" :name="`variants[${index}][size]`" placeholder="S, M, 42..." class="block w-full bg-transparent border border-lightBorder dark:border-darkBorder rounded p-2 text-sm outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors">
                    </div>
                    <div class="flex-1">
                        <label class="block text-[10px] font-bold tracking-widest uppercase text-lightMuted dark:text-darkMuted mb-2">Warna (Opsional)</label>
                        <input type="text" x-model="variant.color" :name="`variants[${index}][color]`" placeholder="Hitam..." class="block w-full bg-transparent border border-lightBorder dark:border-darkBorder rounded p-2 text-sm outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors">
                    </div>
                    <div class="w-24">
                        <label class="block text-[10px] font-bold tracking-widest uppercase text-lightMuted dark:text-darkMuted mb-2">Stok</label>
                        <input type="number" x-model.number="variant.stock" @input="calculateTotal" :name="`variants[${index}][stock]`" class="block w-full bg-transparent border border-lightBorder dark:border-darkBorder rounded p-2 text-sm outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors">
                    </div>
                    <div>
                        <button type="button" @click="removeVariant(index)" class="border border-red-500 text-red-500 hover:bg-red-500 hover:text-white px-3 py-2 rounded text-[10px] font-bold tracking-widest uppercase transition-colors">
                            Hapus
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <div class="mt-6 border border-lightBorder dark:border-darkBorder p-6 rounded-lg">
        <label class="block text-xs font-bold tracking-[0.2em] uppercase text-lightMuted dark:text-darkMuted mb-3">Upload Gambar (Maks 5)</label>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <template x-for="(image, index) in images" :key="index">
                <div class="relative border border-lightBorder dark:border-darkBorder rounded-lg overflow-hidden aspect-square flex items-center justify-center bg-black/5 dark:bg-white/5">
                    
                    <!-- Always render input to hold the file -->
                    <input type="file" name="images[]" accept="image/*" 
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                           @change="handleImageUpload($event, index)"
                           :disabled="image.preview !== null">

                    <!-- Preview Mode -->
                    <template x-if="image.preview">
                        <div class="w-full h-full relative z-20">
                            <img :src="image.preview" class="w-full h-full object-cover" />
                            <button type="button" @click="removeImage(index)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold shadow-md hover:bg-red-600">×</button>
                        </div>
                    </template>
                    
                    <!-- Empty Slot Mode -->
                    <template x-if="!image.preview">
                        <div class="flex flex-col items-center justify-center text-lightMuted dark:text-darkMuted pointer-events-none">
                            <span class="text-2xl mb-1">+</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest">Pilih</span>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>

    <div class="pt-6 flex justify-end gap-4 border-t border-lightBorder dark:border-darkBorder">
        <a href="{{ url('/admin/products') }}" class="px-8 py-3 text-[10px] uppercase tracking-[0.2em] font-bold text-lightMuted hover:text-lightMain dark:text-darkMuted dark:hover:text-darkMain transition-colors flex items-center">Batal</a>
        <button type="submit" class="w-full md:w-auto bg-lightMain dark:bg-darkMain text-lightBg dark:text-darkBg px-8 py-4 rounded-lg hover:opacity-90 font-bold uppercase tracking-[0.2em] text-xs transition-colors">
            Simpan Perubahan
        </button>
    </div>
</form>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('productEditForm', (initialStock, initialVariants) => ({
            totalStock: initialStock,
            variants: initialVariants,
            images: [{ file: null, preview: null }],
            
            addVariant() {
                this.variants.push({ size: '', color: '', stock: 0 });
                this.calculateTotal();
            },
            removeVariant(index) {
                this.variants.splice(index, 1);
                this.calculateTotal();
            },
            calculateTotal() {
                if (this.variants.length > 0) {
                    this.totalStock = this.variants.reduce((sum, item) => sum + (parseInt(item.stock) || 0), 0);
                }
            },
            handleImageUpload(event, index) {
                const file = event.target.files[0];
                if (file) {
                    this.images[index].file = file;
                    this.images[index].preview = URL.createObjectURL(file);
                    
                    // Move the actual file to the hidden input if we dynamically created it (since Alpine handles the DOM)
                    // In a standard form post, the input @change already holds the file.
                    
                    if (index === this.images.length - 1 && this.images.length < 5) {
                        this.images.push({ file: null, preview: null });
                    }
                }
            },
            removeImage(index) {
                // To safely remove a file input in a traditional form, we need to clear its value or remove the node.
                this.images.splice(index, 1);
                
                if (this.images.length < 5 && this.images[this.images.length - 1].preview !== null) {
                    this.images.push({ file: null, preview: null });
                }
                if (this.images.length === 0) {
                    this.images.push({ file: null, preview: null });
                }
            }
        }));
    });
</script>
@endsection
