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
                        <h4 class="font-semibold text-lg uppercase tracking-widest">{{ $store->name }}</h4>
                        <label class="flex items-center cursor-pointer">
                            <span class="mr-2 text-xs font-bold uppercase">Tampilkan:</span>
                            <input type="checkbox" name="online_stores[{{ $store->id }}][is_active]" value="1" {{ $store->is_active ? 'checked' : '' }} class="w-5 h-5 accent-lightMain dark:accent-darkMain">
                        </label>
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
        </div>
    </div>

    <!-- Offline Stores -->
    <div class="p-8 border-minimal border-lightBorder bg-black/5 rounded text-sm mb-8">
        <h3 class="font-bold text-lg mb-4">Pengaturan Offline Stores</h3>
        <div class="space-y-6">
            @foreach($offlineStores as $store)
                <div class="border border-lightBorder dark:border-darkBorder p-4 rounded bg-lightBg dark:bg-[#1E1E1E]">
                    <div class="flex justify-between items-center mb-4 border-b border-lightBorder dark:border-darkBorder pb-2">
                        <h4 class="font-semibold text-lg uppercase tracking-widest">{{ $store->name }}</h4>
                        <label class="flex items-center cursor-pointer">
                            <span class="mr-2 text-xs font-bold uppercase">Tampilkan:</span>
                            <input type="checkbox" name="offline_stores[{{ $store->id }}][is_active]" value="1" {{ $store->is_active ? 'checked' : '' }} class="w-5 h-5 accent-lightMain dark:accent-darkMain">
                        </label>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mb-8">
        <button type="submit" class="bg-lightMain dark:bg-darkMain text-lightBg dark:text-darkBg px-8 py-3 rounded font-bold hover:opacity-90 transition uppercase tracking-widest">Simpan Perubahan</button>
    </div>
</form>
@endsection


