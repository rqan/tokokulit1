@extends('admin.layout.main')

@section('title', 'DETAIL PESANAN')

@section('content')
<div class="mb-8">
    <div class="mb-4">
        <a href="{{ url('/admin/orders') }}" class="text-xs uppercase tracking-widest font-bold hover:underline">&larr; Kembali ke Daftar Pesanan</a>
    </div>
    
    @php
        $badgeColor = match($order->status) {
            'pending_confirmation' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
            'awaiting_payment' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
            'payment_uploaded' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
            'processing' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300',
            'shipped' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-300',
            'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
            'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300'
        };
        
        $statusLabels = [
            'pending_confirmation' => 'Menunggu Konfirmasi',
            'awaiting_payment' => 'Menunggu Pembayaran',
            'payment_uploaded' => 'Pembayaran Terupload',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan'
        ];
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <!-- Section 1: Order Header -->
            <div class="border-minimal border-lightBorder dark:border-darkBorder rounded p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="display-font text-2xl mb-2">Invoice: @if($order->invoice_number)<span class="font-mono">{{ $order->invoice_number }}</span>@else<span class="font-mono text-red-500 uppercase">Belum Ada</span>@endif</h2>
                        <div class="text-sm text-lightMuted dark:text-darkMuted">{{ $order->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <span class="px-3 py-1.5 rounded text-sm font-semibold {{ $badgeColor }}">
                        {{ $statusLabels[$order->status] ?? $order->status }}
                    </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-xs uppercase tracking-widest font-bold mb-3 border-b-minimal border-lightBorder dark:border-darkBorder pb-2">Informasi Pelanggan</h3>
                        <p class="font-semibold">{{ $order->shipping_name }}</p>
                        <p class="text-sm mt-1">{{ $order->shipping_email }}</p>
                        <p class="text-sm mt-1">
                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $order->shipping_phone)) }}" target="_blank" class="hover:underline text-green-600 dark:text-green-400">
                                {{ $order->shipping_phone }} (WhatsApp)
                            </a>
                        </p>
                    </div>
                    <div>
                        <h3 class="text-xs uppercase tracking-widest font-bold mb-3 border-b-minimal border-lightBorder dark:border-darkBorder pb-2">Alamat Pengiriman</h3>
                        <p class="text-sm">{{ $order->shipping_address }}</p>
                        <p class="text-sm mt-1">{{ $order->shipping_city }}, {{ $order->shipping_province }}</p>
                        <p class="text-sm mt-1">{{ $order->shipping_postal_code }}</p>
                    </div>
                </div>
                
                @if($order->notes)
                <div class="mt-6 pt-4 border-t-minimal border-lightBorder dark:border-darkBorder">
                    <h3 class="text-xs uppercase tracking-widest font-bold mb-2">Catatan Pesanan</h3>
                    <p class="text-sm bg-black/5 dark:bg-white/5 p-3 rounded">{{ $order->notes }}</p>
                </div>
                @endif
            </div>

            <!-- Section 2: Order Items Table -->
            <div class="border-minimal border-lightBorder dark:border-darkBorder rounded p-6">
                <h3 class="text-xs uppercase tracking-widest font-bold mb-4 border-b-minimal border-lightBorder dark:border-darkBorder pb-2">Item Pesanan</h3>
                
                <div class="overflow-x-auto mb-6">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b-minimal border-lightBorder dark:border-darkBorder text-[10px] uppercase tracking-widest text-lightMuted dark:text-darkMuted">
                            <tr>
                                <th class="pb-3 font-semibold">Produk</th>
                                <th class="pb-3 font-semibold text-right">Harga</th>
                                <th class="pb-3 font-semibold text-center">Qty</th>
                                <th class="pb-3 font-semibold text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-lightBorder dark:divide-darkBorder">
                            @foreach($order->items as $item)
                            <tr>
                                <td class="py-4">
                                    <div class="font-semibold">{{ $item->product_name }}</div>
                                </td>
                                <td class="py-4 text-right font-mono">Rp{{ number_format($item->product_price, 0, ',', '.') }}</td>
                                <td class="py-4 text-center">{{ $item->quantity }}</td>
                                <td class="py-4 text-right font-mono font-semibold">Rp{{ number_format($item->product_price * $item->quantity, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="w-full md:w-1/2 ml-auto space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-lightMuted dark:text-darkMuted">Subtotal Produk:</span>
                        <span class="font-mono">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-lightMuted dark:text-darkMuted">Ongkos Kirim:</span>
                        <span class="font-mono">{{ $order->shipping_fee > 0 ? 'Rp' . number_format($order->shipping_fee, 0, ',', '.') : ($order->status == 'pending_confirmation' ? 'Belum ditentukan' : 'Rp0') }}</span>
                    </div>
                    @if($order->additional_fee > 0)
                    <div class="flex justify-between text-amber-600 dark:text-amber-400">
                        <span>Biaya Tambahan:
                            @if($order->additional_fee_note)
                            <br><span class="text-[10px]">{{ $order->additional_fee_note }}</span>
                            @endif
                        </span>
                        <span class="font-mono">Rp{{ number_format($order->additional_fee, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between border-t-minimal border-lightBorder dark:border-darkBorder pt-3 font-bold text-base">
                        <span>Total Keseluruhan:</span>
                        <span class="font-mono">Rp{{ number_format($order->grand_total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Section 4: Payment History -->
            @if($order->payments && $order->payments->count() > 0)
            <div class="border-minimal border-lightBorder dark:border-darkBorder rounded p-6">
                <h3 class="text-xs uppercase tracking-widest font-bold mb-4 border-b-minimal border-lightBorder dark:border-darkBorder pb-2">Riwayat Pembayaran</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b-minimal border-lightBorder dark:border-darkBorder text-[10px] uppercase tracking-widest text-lightMuted dark:text-darkMuted">
                            <tr>
                                <th class="pb-3 font-semibold">Tanggal</th>
                                <th class="pb-3 font-semibold">Metode</th>
                                <th class="pb-3 font-semibold">Jumlah</th>
                                <th class="pb-3 font-semibold">Status</th>
                                <th class="pb-3 font-semibold">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-lightBorder dark:divide-darkBorder">
                            @foreach($order->payments as $payment)
                            <tr>
                                <td class="py-3">{{ $payment->created_at->format('d M Y H:i') }}</td>
                                <td class="py-3">{{ $payment->payment_method_name ?? 'Transfer' }}</td>
                                <td class="py-3 font-mono">Rp{{ number_format($payment->amount, 0, ',', '.') }}</td>
                                <td class="py-3">
                                    @if($payment->status == 'verified')
                                        <span class="text-green-600 dark:text-green-400 font-semibold text-xs">Terverifikasi</span>
                                    @elseif($payment->status == 'rejected')
                                        <span class="text-red-600 dark:text-red-400 font-semibold text-xs">Ditolak</span>
                                    @else
                                        <span class="text-amber-600 dark:text-amber-400 font-semibold text-xs">Menunggu</span>
                                    @endif
                                </td>
                                <td class="py-3 text-xs">{{ $payment->rejection_note ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Section 5: Audit Trail -->
            <!-- <div class="border-minimal border-lightBorder dark:border-darkBorder rounded p-6">
                Future implementation for audit trail / activity log.
            </div> -->
        </div>

        <!-- Section 3: Admin Actions Sidebar -->
        <div class="space-y-6">
            <div class="border-minimal border-lightBorder dark:border-darkBorder rounded p-6 sticky top-6 bg-lightBg dark:bg-darkBg">
                <h3 class="text-xs uppercase tracking-widest font-bold mb-4 border-b-minimal border-lightBorder dark:border-darkBorder pb-2">Tindakan Admin</h3>
                
                @if($order->status == 'pending_confirmation')
                    <form action="{{ url('/admin/orders/' . $order->id . '/shipping') }}" method="POST" class="mb-6 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold mb-1">Ongkos Kirim (Rp)</label>
                            <input type="number" name="shipping_fee" value="{{ $order->shipping_fee }}" class="w-full p-2 bg-transparent border-minimal border-lightBorder dark:border-darkBorder rounded focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold mb-1">Biaya Tambahan (Opsional) (Rp)</label>
                            <input type="number" name="additional_fee" value="{{ $order->additional_fee }}" class="w-full p-2 bg-transparent border-minimal border-lightBorder dark:border-darkBorder rounded focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold mb-1">Catatan Biaya Tambahan</label>
                            <input type="text" name="additional_fee_note" value="{{ $order->additional_fee_note }}" class="w-full p-2 bg-transparent border-minimal border-lightBorder dark:border-darkBorder rounded focus:outline-none" placeholder="Misal: Biaya packing kayu">
                        </div>
                        <button type="submit" class="w-full py-3 bg-black/10 dark:bg-white/10 hover:bg-black/20 dark:hover:bg-white/20 transition-colors text-xs font-bold uppercase tracking-widest rounded">
                            Simpan Ongkir
                        </button>
                    </form>

                    @if($order->shipping_fee !== null)
                    <form action="{{ url('/admin/orders/' . $order->id . '/approve') }}" method="POST" class="mb-4">
                        @csrf
                        <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white transition-colors text-xs font-bold uppercase tracking-widest rounded">
                            Approve & Kirim Invoice
                        </button>
                    </form>
                    @endif

                    <form action="{{ url('/admin/orders/' . $order->id . '/cancel') }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?');">
                        @csrf
                        <button type="submit" class="w-full py-3 border-minimal border-red-500 text-red-500 hover:bg-red-500 hover:text-white transition-colors text-xs font-bold uppercase tracking-widest rounded">
                            Batalkan Pesanan
                        </button>
                    </form>
                
                @elseif($order->status == 'awaiting_payment')
                    <div class="bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 p-4 rounded text-sm mb-6 font-medium">
                        Menunggu pembayaran dari pelanggan...
                    </div>
                    
                    @php
                        $waMessage = "Halo {$order->shipping_name},\n\nTerima kasih telah memesan di TOKO RAFI" . number_format($order->grand_total, 0, ',', '.') . "\n\nSilakan cek invoice lengkap dan cara pembayaran melalui link berikut:\n" . url("/invoice/{$order->invoice_number}");
                        $waLink = "https://wa.me/" . preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $order->shipping_phone)) . "?text=" . urlencode($waMessage);
                    @endphp
                    
                    <a href="{{ $waLink }}" target="_blank" class="block text-center w-full py-3 bg-green-600 hover:bg-green-700 text-white transition-colors text-xs font-bold uppercase tracking-widest rounded mb-4">
                        Kirim Invoice via WA
                    </a>
                    
                    <form action="{{ url('/admin/orders/' . $order->id . '/cancel') }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?');">
                        @csrf
                        <button type="submit" class="w-full py-3 border-minimal border-red-500 text-red-500 hover:bg-red-500 hover:text-white transition-colors text-xs font-bold uppercase tracking-widest rounded">
                            Batalkan Pesanan
                        </button>
                    </form>

                @elseif($order->status == 'payment_uploaded')
                    @php
                        $latestPayment = $order->payments()->latest()->first();
                    @endphp
                    
                    <div class="mb-6 space-y-3">
                        <div class="text-sm font-semibold">Bukti Pembayaran:</div>
                        @if($latestPayment && $latestPayment->proof_image)
                            <a href="{{ asset('storage/' . $latestPayment->proof_image) }}" target="_blank" class="block border-minimal border-lightBorder dark:border-darkBorder rounded overflow-hidden hover:opacity-80 transition-opacity">
                                <img src="{{ asset('storage/' . $latestPayment->proof_image) }}" alt="Bukti Pembayaran" class="w-full h-auto">
                            </a>
                        @else
                            <div class="p-4 bg-black/5 dark:bg-white/5 rounded text-sm text-center">Gambar tidak tersedia</div>
                        @endif
                        
                        <div class="text-sm bg-black/5 dark:bg-white/5 p-3 rounded">
                            <p><strong>Metode:</strong> {{ $latestPayment->payment_method_name ?? '-' }}</p>
                            <p><strong>Jumlah:</strong> Rp{{ number_format($latestPayment->amount ?? 0, 0, ',', '.') }}</p>
                            <p><strong>Tanggal:</strong> {{ $latestPayment ? $latestPayment->created_at->format('d M Y H:i') : '-' }}</p>
                        </div>
                    </div>
                    
                    <form action="{{ url('/admin/orders/' . $order->id . '/verify-payment') }}" method="POST" class="mb-4">
                        @csrf
                        <button type="submit" class="w-full py-3 bg-green-600 hover:bg-green-700 text-white transition-colors text-xs font-bold uppercase tracking-widest rounded">
                            Verifikasi Pembayaran
                        </button>
                    </form>
                    
                    <form action="{{ url('/admin/orders/' . $order->id . '/reject-payment') }}" method="POST" class="space-y-3" onsubmit="return confirm('Yakin ingin menolak pembayaran ini?');">
                        @csrf
                        <div>
                            <textarea name="rejection_note" class="w-full p-2 bg-transparent border-minimal border-lightBorder dark:border-darkBorder rounded focus:outline-none text-sm" placeholder="Alasan penolakan..." required rows="2"></textarea>
                        </div>
                        <button type="submit" class="w-full py-3 border-minimal border-red-500 text-red-500 hover:bg-red-500 hover:text-white transition-colors text-xs font-bold uppercase tracking-widest rounded">
                            Tolak Pembayaran
                        </button>
                    </form>

                @elseif($order->status == 'processing')
                    <form action="{{ url('/admin/orders/' . $order->id . '/tracking') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold mb-1">Kurir Pengiriman</label>
                            <input type="text" name="shipping_courier" value="{{ $order->shipping_courier }}" class="w-full p-2 bg-transparent border-minimal border-lightBorder dark:border-darkBorder rounded focus:outline-none" required placeholder="JNE, Sicepat, J&T, dll">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold mb-1">Nomor Resi</label>
                            <input type="text" name="tracking_number" value="{{ $order->tracking_number }}" class="w-full p-2 bg-transparent border-minimal border-lightBorder dark:border-darkBorder rounded focus:outline-none" required>
                        </div>
                        <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white transition-colors text-xs font-bold uppercase tracking-widest rounded mt-2">
                            Update Resi & Kirim
                        </button>
                    </form>
                    
                @elseif($order->status == 'shipped')
                    <div class="mb-6 p-4 bg-black/5 dark:bg-white/5 rounded">
                        <div class="text-xs uppercase tracking-widest font-bold mb-2">Info Pengiriman</div>
                        <p class="text-sm"><strong>Kurir:</strong> {{ $order->shipping_courier }}</p>
                        <p class="text-sm"><strong>Resi:</strong> <span class="font-mono bg-black/10 dark:bg-white/10 px-1 rounded">{{ $order->tracking_number }}</span></p>
                    </div>
                    
                    <form action="{{ url('/admin/orders/' . $order->id . '/complete') }}" method="POST" onsubmit="return confirm('Tandai pesanan selesai? Pastikan barang sudah diterima pelanggan.');">
                        @csrf
                        <button type="submit" class="w-full py-3 bg-green-600 hover:bg-green-700 text-white transition-colors text-xs font-bold uppercase tracking-widest rounded">
                            Tandai Selesai
                        </button>
                    </form>
                    
                @elseif($order->status == 'completed')
                    @if($order->rating)
                        <div class="mb-4">
                            <div class="text-xs uppercase tracking-widest font-bold mb-2">Ulasan Pelanggan</div>
                            <div class="flex items-center mb-2 text-amber-500">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $order->rating->rating)
                                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-300 dark:text-gray-600 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endif
                                @endfor
                            </div>
                            <p class="text-sm italic">"{{ $order->rating->review }}"</p>
                        </div>
                    @else
                        <div class="mb-4">
                            <div class="text-xs uppercase tracking-widest font-bold mb-3">Kirim Link Rating</div>
                            <form action="{{ url('/admin/orders/' . $order->id . '/send-rating') }}" method="POST" class="space-y-3">
                                @csrf
                                <input type="hidden" name="channel" id="rating_channel" value="whatsapp">
                                
                                <button type="submit" onclick="document.getElementById('rating_channel').value='whatsapp'" class="w-full py-3 bg-green-600 hover:bg-green-700 text-white transition-colors text-xs font-bold uppercase tracking-widest rounded flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    Kirim Via WhatsApp
                                </button>
                                
                                <button type="submit" onclick="document.getElementById('rating_channel').value='email'" class="w-full py-3 bg-black/10 dark:bg-white/10 hover:bg-black/20 dark:hover:bg-white/20 transition-colors text-xs font-bold uppercase tracking-widest rounded flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    Kirim Via Email
                                </button>
                            </form>
                        </div>
                        
                        @if($order->rating_sent_at)
                            <div class="text-[10px] text-lightMuted dark:text-darkMuted">
                                Link terakhir dikirim: {{ \Carbon\Carbon::parse($order->rating_sent_at)->format('d M Y H:i') }}
                            </div>
                        @endif
                    @endif
                    
                @else
                    <div class="text-sm text-lightMuted dark:text-darkMuted italic">
                        Tidak ada tindakan tersedia untuk status ini.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

