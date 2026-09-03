<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->invoice_number }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind-config.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        @media print {
            body { background-color: white !important; color: black !important; }
            .no-print { display: none !important; }
            * { border-color: #e5e7eb !important; }
        }
    </style>
</head>
<body class="bg-gray-100 py-10 antialiased font-sans text-gray-800">

    <div class="max-w-4xl mx-auto bg-white p-10 md:p-16 shadow-lg border border-gray-200">
        
        <!-- Print Button -->
        <div class="mb-8 flex justify-end no-print">
            <button onclick="window.print()" class="px-6 py-2 bg-black text-white text-xs tracking-widest uppercase font-bold hover:bg-gray-800 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Invoice
            </button>
        </div>

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end border-b-2 border-gray-200 pb-8 mb-8">
            <div>
                <h1 class="display-font text-5xl md:text-6xl tracking-tight mb-2">TOKO RAFI</h1>
                <p class="text-xs text-gray-500 uppercase tracking-widest">Official Web Store</p>
            </div>
            <div class="text-right mt-6 md:mt-0">
                <h2 class="text-3xl font-bold uppercase tracking-wider mb-2">INVOICE</h2>
                <p class="text-xs tracking-widest text-gray-500 uppercase">{{ $order->invoice_number }}</p>
                <p class="text-xs tracking-widest text-gray-500 uppercase">{{ $order->created_at->format('d F Y') }}</p>
            </div>
        </div>

        <!-- Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-12">
            <div>
                <h3 class="text-[10px] tracking-widest text-gray-400 uppercase font-bold mb-4">Ditagihkan Kepada (Bill To)</h3>
                <p class="font-bold uppercase text-sm mb-1">{{ $order->shipping_name }}</p>
                <p class="text-sm mb-1">{{ $order->shipping_phone }}</p>
                <p class="text-sm mb-1">{{ $order->shipping_email }}</p>
                <p class="text-sm leading-relaxed mt-2 text-gray-600">{{ $order->shipping_address }}</p>
            </div>
            <div class="md:text-right">
                <h3 class="text-[10px] tracking-widest text-gray-400 uppercase font-bold mb-4">Status Pesanan</h3>
                <span class="inline-block px-4 py-1 border-2 border-gray-800 text-xs font-bold tracking-widest uppercase">
                    {{ $order->status_label ?? $order->status }}
                </span>
                @if($order->tracking_number)
                <div class="mt-4">
                    <h3 class="text-[10px] tracking-widest text-gray-400 uppercase font-bold mb-1">Nomor Resi</h3>
                    <p class="font-bold text-sm">{{ $order->tracking_number }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <div class="mb-12">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b-2 border-gray-800 text-[10px] tracking-widest uppercase text-gray-500">
                        <th class="py-3 px-2 font-bold w-12 text-center">No</th>
                        <th class="py-3 px-4 font-bold">Produk</th>
                        <th class="py-3 px-4 font-bold text-right">Harga</th>
                        <th class="py-3 px-4 font-bold text-center">Qty</th>
                        <th class="py-3 px-4 font-bold text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach($order->items as $index => $item)
                    <tr class="border-b border-gray-200">
                        <td class="py-4 px-2 text-center text-gray-500">{{ $index + 1 }}</td>
                        <td class="py-4 px-4 font-bold uppercase">{{ $item->product_name }}</td>
                        <td class="py-4 px-4 text-right">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="py-4 px-4 text-center">{{ $item->quantity }}</td>
                        <td class="py-4 px-4 text-right font-bold">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="flex justify-end mb-16">
            <div class="w-full md:w-1/2 space-y-4 text-sm tracking-widest uppercase">
                <div class="flex justify-between border-b border-gray-200 pb-2">
                    <span class="text-gray-500">Subtotal</span>
                    <span>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-200 pb-2">
                    <span class="text-gray-500">Ongkos Kirim</span>
                    <span>{{ $order->shipping_fee > 0 ? 'Rp' . number_format($order->shipping_fee, 0, ',', '.') : '-' }}</span>
                </div>
                @if($order->additional_fee > 0)
                <div class="flex justify-between border-b border-gray-200 pb-2">
                    <span class="text-gray-500">Biaya Tambahan</span>
                    <span>Rp{{ number_format($order->additional_fee, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between border-b-2 border-gray-800 pb-2 pt-2 text-lg font-bold">
                    <span>Grand Total</span>
                    <span>Rp{{ number_format($order->grand_total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Payment Instructions -->
        @if(in_array($order->status, ['pending', 'approved', 'awaiting_payment']) && isset($paymentMethods) && count($paymentMethods) > 0)
        <div class="border-2 border-gray-200 p-8 mb-12 break-inside-avoid">
            <h3 class="display-font text-2xl uppercase mb-6 text-center">Instruksi Pembayaran</h3>
            <p class="text-center text-xs tracking-widest uppercase text-gray-500 mb-8">Silakan transfer sesuai dengan nominal <strong>Grand Total</strong> ke salah satu rekening di bawah ini:</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($paymentMethods as $method)
                    <div class="text-center p-4 bg-gray-50">
                        <p class="font-bold uppercase text-lg mb-2">{{ $method->bank_name ?? 'Metode' }}</p>
                        <p class="text-xl tracking-widest font-mono mb-2">{{ $method->account_number }}</p>
                        <p class="text-xs uppercase text-gray-500">a.n. {{ $method->account_name }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="text-center pt-8 border-t-2 border-gray-200 text-[10px] tracking-[0.2em] uppercase text-gray-400">
            <p class="mb-2">Terima kasih atas pesanan Anda!</p>
            <p>Jika Anda memiliki pertanyaan mengenai invoice ini, silakan hubungi kami.</p>
        </div>
    </div>

</body>
</html>


