@extends('admin.layout.main')
@section('title', $title ?? '')
@section('content')

<form action="{{ url('admin/cms/update') }}" method="POST">
    @csrf
    
    <!-- Online Stores -->
    <div class="p-8 border-minimal border-lightBorder bg-black/5 rounded text-sm mb-8">
        <h3 class="font-bold text-lg mb-4">Pengaturan Online Stores</h3>
        <div class="space-y-6">
            @foreach($onlineStores as $store)
                <div class="border border-lightBorder dark:border-darkBorder p-4 rounded bg-lightBg dark:bg-[#1E1E1E]">
                    <div class="flex justify-between items-center mb-4 border-b border-lightBorder dark:border-darkBorder pb-2">
                        <h4 class="font-semibold text-lg uppercase tracking-widest">{{ $store->name ?: '(Toko Tanpa Nama)' }}</h4>
                        <div class="flex items-center space-x-4">
                            <button type="submit" form="delete-form-online-{{ $store->id }}" onclick="return confirm('Hapus toko online ini secara permanen?')" class="text-xs font-bold uppercase tracking-widest text-red-500 hover:text-red-700">
                                Hapus
                            </button>
                            <label class="flex items-center cursor-pointer">
                                <span class="mr-2 text-xs font-bold uppercase">Tampilkan:</span>
                                <input type="checkbox" name="online_stores[{{ $store->id }}][is_active]" value="1" {{ $store->is_active ? 'checked' : '' }} class="w-5 h-5 accent-lightMain dark:accent-darkMain">
                            </label>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold mb-1">Nama Toko</label>
                            <input type="text" name="online_stores[{{ $store->id }}][name]" value="{{ $store->name }}" class="w-full bg-transparent border border-lightBorder dark:border-darkBorder p-2 rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain">
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1">Keterangan</label>
                            <input type="text" name="online_stores[{{ $store->id }}][description]" value="{{ $store->description }}" class="w-full bg-transparent border border-lightBorder dark:border-darkBorder p-2 rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain">
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1">URL / Link</label>
                            <input type="text" name="online_stores[{{ $store->id }}][url]" value="{{ $store->url }}" class="w-full bg-transparent border border-lightBorder dark:border-darkBorder p-2 rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain">
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Tambah Online Store Baru -->
            <div class="border border-lightBorder dark:border-darkBorder p-4 rounded bg-lightBg dark:bg-[#1E1E1E] mt-4 border-dashed">
                <div class="flex justify-between items-center mb-4 border-b border-lightBorder dark:border-darkBorder pb-2">
                    <h4 class="font-semibold text-lg uppercase tracking-widest text-lightMain dark:text-darkMain">+ Tambah Toko Baru</h4>
                    <label class="flex items-center cursor-pointer">
                        <span class="mr-2 text-xs font-bold uppercase">Tampilkan:</span>
                        <input type="checkbox" name="new_online_store[is_active]" value="1" checked class="w-5 h-5 accent-lightMain dark:accent-darkMain">
                    </label>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold mb-1">Nama Toko</label>
                        <input type="text" name="new_online_store[name]" placeholder="Nama Toko (opsional)" class="w-full bg-transparent border border-lightBorder dark:border-darkBorder p-2 rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain">
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1">Keterangan</label>
                        <input type="text" name="new_online_store[description]" placeholder="Keterangan singkat" class="w-full bg-transparent border border-lightBorder dark:border-darkBorder p-2 rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain">
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1">URL / Link</label>
                        <input type="text" name="new_online_store[url]" placeholder="https://" class="w-full bg-transparent border border-lightBorder dark:border-darkBorder p-2 rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain">
                    </div>
                </div>
                <p class="text-[10px] text-lightMuted dark:text-darkMuted mt-3">* Isi form di atas dan klik Simpan untuk menambah toko online baru.</p>
            </div>
        </div>
    </div>

    <!-- Offline Stores -->
    <div class="p-8 border-minimal border-lightBorder bg-black/5 rounded text-sm mb-8">
        <h3 class="font-bold text-lg mb-4">Pengaturan Offline Stores</h3>
        <div class="space-y-6">
            @foreach($offlineStores as $store)
                <div class="border border-lightBorder dark:border-darkBorder p-4 rounded bg-lightBg dark:bg-[#1E1E1E]">
                    <div class="flex justify-between items-center mb-4 border-b border-lightBorder dark:border-darkBorder pb-2">
                        <h4 class="font-semibold text-lg uppercase tracking-widest">{{ $store->name ?: '(Toko Tanpa Nama)' }}</h4>
                        <div class="flex items-center space-x-4">
                            <button type="submit" form="delete-form-offline-{{ $store->id }}" onclick="return confirm('Hapus toko offline ini secara permanen?')" class="text-xs font-bold uppercase tracking-widest text-red-500 hover:text-red-700">
                                Hapus
                            </button>
                            <label class="flex items-center cursor-pointer">
                                <span class="mr-2 text-xs font-bold uppercase">Tampilkan:</span>
                                <input type="checkbox" name="offline_stores[{{ $store->id }}][is_active]" value="1" {{ $store->is_active ? 'checked' : '' }} class="w-5 h-5 accent-lightMain dark:accent-darkMain">
                            </label>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-bold mb-1">Nama Toko</label>
                            <input type="text" name="offline_stores[{{ $store->id }}][name]" value="{{ $store->name }}" class="w-full bg-transparent border border-lightBorder dark:border-darkBorder p-2 rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain">
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1">Keterangan</label>
                            <input type="text" name="offline_stores[{{ $store->id }}][description]" value="{{ $store->description }}" class="w-full bg-transparent border border-lightBorder dark:border-darkBorder p-2 rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain">
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1">Alamat Lengkap</label>
                            <input type="text" name="offline_stores[{{ $store->id }}][address]" value="{{ $store->address }}" class="w-full bg-transparent border border-lightBorder dark:border-darkBorder p-2 rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain">
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1">Link Google Maps</label>
                            <input type="text" name="offline_stores[{{ $store->id }}][map_link]" value="{{ $store->map_link }}" placeholder="https://maps.app.goo.gl/..." class="w-full bg-transparent border border-lightBorder dark:border-darkBorder p-2 rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain">
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Tambah Offline Store Baru -->
            <div class="border border-lightBorder dark:border-darkBorder p-4 rounded bg-lightBg dark:bg-[#1E1E1E] mt-4 border-dashed">
                <div class="flex justify-between items-center mb-4 border-b border-lightBorder dark:border-darkBorder pb-2">
                    <h4 class="font-semibold text-lg uppercase tracking-widest text-lightMain dark:text-darkMain">+ Tambah Toko Baru</h4>
                    <label class="flex items-center cursor-pointer">
                        <span class="mr-2 text-xs font-bold uppercase">Tampilkan:</span>
                        <input type="checkbox" name="new_offline_store[is_active]" value="1" checked class="w-5 h-5 accent-lightMain dark:accent-darkMain">
                    </label>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold mb-1">Nama Toko</label>
                        <input type="text" name="new_offline_store[name]" placeholder="Nama Toko (opsional)" class="w-full bg-transparent border border-lightBorder dark:border-darkBorder p-2 rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain">
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1">Keterangan</label>
                        <input type="text" name="new_offline_store[description]" placeholder="Keterangan singkat" class="w-full bg-transparent border border-lightBorder dark:border-darkBorder p-2 rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain">
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1">Alamat Lengkap</label>
                        <input type="text" name="new_offline_store[address]" placeholder="Alamat offline store" class="w-full bg-transparent border border-lightBorder dark:border-darkBorder p-2 rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain">
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1">Link Google Maps</label>
                        <input type="text" name="new_offline_store[map_link]" placeholder="https://maps.app.goo.gl/..." class="w-full bg-transparent border border-lightBorder dark:border-darkBorder p-2 rounded focus:outline-none focus:border-lightMain dark:focus:border-darkMain">
                    </div>
                </div>
                <p class="text-[10px] text-lightMuted dark:text-darkMuted mt-3">* Isi form di atas dan klik Simpan untuk menambah toko offline baru.</p>
            </div>
        </div>
    </div>

    <div class="mb-8">
        <button type="submit" class="bg-lightMain dark:bg-darkMain text-lightBg dark:text-darkBg px-8 py-3 rounded font-bold hover:opacity-90 transition uppercase tracking-widest">Simpan Perubahan</button>
    </div>
</form>

@foreach($onlineStores as $store)
    <form id="delete-form-online-{{ $store->id }}" action="{{ url('admin/cms/delete-store') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="type" value="online">
        <input type="hidden" name="id" value="{{ $store->id }}">
    </form>
@endforeach

@foreach($offlineStores as $store)
    <form id="delete-form-offline-{{ $store->id }}" action="{{ url('admin/cms/delete-store') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="type" value="offline">
        <input type="hidden" name="id" value="{{ $store->id }}">
    </form>
@endforeach

@endsection


