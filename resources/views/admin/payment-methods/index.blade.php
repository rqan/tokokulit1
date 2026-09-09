@extends('admin.layout.main')

@section('title', 'Metode Pembayaran')

@section('content')
<div class="mb-8">
    <div class="bg-black/5 dark:bg-white/5 border-minimal border-lightBorder dark:border-darkBorder rounded p-6 mb-8">
        <h3 class="text-xs uppercase tracking-widest font-bold mb-4 border-b-minimal border-lightBorder dark:border-darkBorder pb-2">Tambah Metode Baru</h3>
        
        <form action="{{ url('/admin/payment-methods') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold mb-1">Tipe</label>
                    <select name="type" id="payment_type" class="w-full p-2 bg-transparent border-minimal border-lightBorder dark:border-darkBorder rounded focus:outline-none" required onchange="toggleFields()">
                        <option value="bank_transfer" class="bg-lightBg dark:bg-darkBg">Bank Transfer</option>
                        <option value="qris" class="bg-lightBg dark:bg-darkBg">QRIS</option>
                    </select>
                </div>
                
                <div id="bank_name_field">
                    <label class="block text-xs font-semibold mb-1">Nama Bank (misal: BCA)</label>
                    <input type="text" name="bank_name" class="w-full p-2 bg-transparent border-minimal border-lightBorder dark:border-darkBorder rounded focus:outline-none">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="bank_details_fields">
                <div>
                    <label class="block text-xs font-semibold mb-1">Nomor Rekening</label>
                    <input type="text" name="account_number" class="w-full p-2 bg-transparent border-minimal border-lightBorder dark:border-darkBorder rounded focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Atas Nama (Pemilik Rekening)</label>
                    <input type="text" name="account_holder" class="w-full p-2 bg-transparent border-minimal border-lightBorder dark:border-darkBorder rounded focus:outline-none">
                </div>
            </div>
            
            <div id="qris_field" class="hidden">
                <label class="block text-xs font-semibold mb-1">Gambar QRIS (Upload)</label>
                <input type="file" name="qris_image" accept="image/*" class="w-full p-2 bg-transparent border-minimal border-lightBorder dark:border-darkBorder rounded focus:outline-none">
            </div>
            
            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked class="w-4 h-4 bg-transparent border-minimal border-lightBorder">
                <label for="is_active" class="text-sm font-semibold">Aktif</label>
            </div>
            
            <div class="pt-4 border-t-minimal border-lightBorder dark:border-darkBorder">
                <button type="submit" class="px-6 py-2 bg-lightMain dark:bg-darkMain text-lightBg dark:text-darkBg text-xs font-bold uppercase tracking-widest rounded transition-colors hover:opacity-90">
                    Simpan
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($paymentMethods as $method)
        <div class="border-minimal border-lightBorder dark:border-darkBorder rounded p-6 flex flex-col {{ !$method->is_active ? 'opacity-60' : '' }}">
            <div class="flex justify-between items-start mb-4">
                <div class="px-2 py-1 bg-black/10 dark:bg-white/10 rounded text-xs font-bold tracking-widest uppercase">
                    {{ $method->type == 'qris' ? 'QRIS' : 'Bank Transfer' }}
                </div>
                <span class="text-xs font-bold {{ $method->is_active ? 'text-green-500' : 'text-red-500' }}">
                    {{ $method->is_active ? 'AKTIF' : 'TIDAK AKTIF' }}
                </span>
            </div>
            
            <div class="flex-1 mb-6">
                @if($method->type == 'qris')
                    <h4 class="text-xl font-bold mb-2">QRIS Code</h4>
                    @if($method->qris_image)
                        <div class="w-full aspect-square bg-white rounded overflow-hidden flex items-center justify-center p-2 mt-2 border-minimal border-lightBorder">
                            <img src="{{ asset('storage/' . $method->qris_image) }}" alt="QRIS" class="max-w-full max-h-full object-contain">
                        </div>
                    @else
                        <div class="text-sm text-red-500 italic mt-2">Gambar QRIS tidak tersedia</div>
                    @endif
                @else
                    <h4 class="text-xl font-bold mb-1">{{ $method->bank_name }}</h4>
                    <p class="font-mono text-lg tracking-wider mb-2">{{ $method->account_number }}</p>
                    <p class="text-sm text-lightMuted dark:text-darkMuted uppercase">A.N. {{ $method->account_holder }}</p>
                @endif
            </div>
            
            <div class="flex gap-2 border-t-minimal border-lightBorder dark:border-darkBorder pt-4 mt-auto">
                <form action="{{ url('/admin/payment-methods/' . $method->id . '/toggle') }}" method="POST" class="flex-1">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full py-2 bg-black/5 dark:bg-white/5 hover:bg-black/10 dark:hover:bg-white/10 transition-colors text-xs font-bold uppercase tracking-widest rounded">
                        {{ $method->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
                
                <form action="{{ url('/admin/payment-methods/' . $method->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus metode pembayaran ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 border-minimal border-red-500 text-red-500 hover:bg-red-500 hover:text-white transition-colors text-xs font-bold uppercase tracking-widest rounded">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
    function toggleFields() {
        const type = document.getElementById('payment_type').value;
        const bankName = document.getElementById('bank_name_field');
        const bankDetails = document.getElementById('bank_details_fields');
        const qris = document.getElementById('qris_field');
        
        if (type === 'qris') {
            bankName.style.display = 'none';
            bankDetails.style.display = 'none';
            qris.style.display = 'block';
        } else {
            bankName.style.display = 'block';
            bankDetails.style.display = 'grid';
            qris.style.display = 'none';
        }
    }
    
    // Initialize on load
    document.addEventListener('DOMContentLoaded', toggleFields);
</script>
@endsection
