<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind-config.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ux.css') }}">
    <script src="{{ asset('js/ux.js') }}"></script>
</head>
<body class="w-full relative antialiased selection:bg-lightMain selection:text-lightBg dark:selection:bg-darkMain dark:selection:text-darkBg bg-lightBg text-lightMain dark:bg-darkBg dark:text-darkMain">

    

    <header class="w-full px-8 py-6 flex justify-between items-center border-b-minimal border-lightBorder dark:border-darkBorder sticky top-0 z-40 bg-lightBg dark:bg-darkBg transition-colors">
        <div class="flex-1 flex justify-start">
            <a href="{{ url('/') }}" class="display-font text-4xl tracking-tight">ENY LEATHER</a>
        </div>
        <div class="flex-1 flex justify-end items-center space-x-6 md:space-x-8 text-[10px] font-semibold tracking-[0.2em] uppercase">
            <a href="{{ url('/katalog?focus_search=1') }}" class="hidden md:block link-hover">Search</a>
            @if(session('isLoggedIn') || Auth::check())
                <a href="{{ url('/pelanggan/cart') }}" class="link-hover">Cart ({{ count(session('cart', [])) }})</a>
                <a href="{{ url('/profile') }}" class="link-hover">Profile</a>
                <button id="themeToggle" class="link-hover uppercase focus:outline-none">Theme</button>
                <a href="{{ url('/logout') }}" class="link-hover text-red-500">Logout</a>
            @else
                <button id="themeToggle" class="link-hover uppercase focus:outline-none">Theme</button>
                <a href="{{ url('/login') }}" class="link-hover">Sign In</a>
            @endif
        </div>
    </header>

    <main class="w-full min-h-[70vh] px-8 py-16 max-w-5xl mx-auto">

        <div class="mb-8">
            <a href="{{ url('/pelanggan/pesanan') }}" class="text-[10px] tracking-widest uppercase hover:opacity-70 transition-colors flex items-center gap-2 w-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Pesanan Saya
            </a>
        </div>

<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-4 border-b-minimal border-lightBorder dark:border-darkBorder pb-8">
            <div>
                <h1 class="display-font text-5xl uppercase mb-2">Detail Pesanan</h1>
                <p class="text-[10px] tracking-widest uppercase text-lightMuted dark:text-darkMuted">
                    @if($order->invoice_number){{ $order->invoice_number }}@else<span class="text-red-500 font-bold">BELUM ADA INVOICE</span>@endif • {{ $order->created_at->format('d M Y, H:i') }}
                </p>
            </div>
            @if($order->invoice_number)
                <a href="{{ url('/invoice/' . $order->invoice_number) }}" target="_blank" class="border border-lightBorder dark:border-darkBorder px-4 py-2 text-[10px] tracking-widest uppercase hover:bg-lightMain dark:hover:bg-darkMain hover:text-lightBg dark:hover:text-darkBg transition-colors">
                    Cetak Invoice
                </a>
            @endif
        </div>

        @if(session('success'))
            <div class="mb-8 p-4 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 text-xs tracking-widest uppercase text-center border border-green-200 dark:border-green-800">
                {{ session('success') }}
            </div>
        @endif

        <!-- Order Status Tracker -->
        <div class="mb-16 border border-lightBorder dark:border-darkBorder p-8 overflow-x-auto">
            <div class="flex justify-between items-center min-w-[600px] relative">
                <div class="absolute left-0 top-1/2 w-full h-px bg-lightBorder dark:bg-darkBorder -z-10"></div>
                
                @php
                    $steps = [
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'awaiting_payment' => 'Menunggu Pembayaran',
                        'payment_uploaded' => 'Verifikasi Bayar',
                        'paid' => 'Dibayar',
                        'processing' => 'Diproses',
                        'shipped' => 'Dikirim',
                        'completed' => 'Selesai'
                    ];
                    
                    // Simple logic for UI step progression
                    $currentStatusIndex = array_search($order->status, array_keys($steps));
                    if($currentStatusIndex === false && $order->status != 'cancelled' && $order->status != 'rejected') $currentStatusIndex = 0;
                @endphp

                @foreach($steps as $key => $label)
                    @php
                        $stepIndex = array_search($key, array_keys($steps));
                        $isCompleted = $stepIndex < $currentStatusIndex;
                        $isCurrent = $stepIndex === $currentStatusIndex;
                    @endphp
                    <div class="flex flex-col items-center bg-lightBg dark:bg-darkBg px-2">
                        <div class="w-4 h-4 rounded-full border border-lightMain dark:border-darkMain {{ $isCompleted || $isCurrent ? 'bg-lightMain dark:bg-darkMain' : 'bg-transparent' }} mb-2"></div>
                        <span class="text-[9px] tracking-widest uppercase {{ $isCurrent ? 'font-bold' : 'text-lightMuted dark:text-darkMuted' }} text-center max-w-[80px]">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            
            <div class="lg:col-span-2 space-y-12">
                <!-- Items List -->
                <div>
                    <h2 class="display-font text-2xl uppercase mb-6 border-b-minimal border-lightBorder dark:border-darkBorder pb-4">Produk</h2>
                    <div class="space-y-6">
                        @foreach($order->items as $item)
                            <div class="flex justify-between items-center pb-6 border-b-minimal border-lightBorder dark:border-darkBorder">
                                <div class="flex gap-4 items-center">
                                    <div class="w-24 h-16 bg-[#E5E5E5] dark:bg-[#1E1E1E] shrink-0">
                                        @if($item->product && $item->product->image_url)
                                            <img src="{{ $item->product->image_url }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-bold uppercase text-sm mb-1">{{ $item->product_name }}</p>
                                        <p class="text-[10px] tracking-widest text-lightMuted dark:text-darkMuted">QTY: {{ $item->quantity }} x Rp{{ number_format($item->product_price, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <div class="font-bold tracking-widest text-sm">
                                    Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Payment Section -->
                @if(in_array($order->status, ['awaiting_payment', 'payment_uploaded']))
                    <div class="border border-lightBorder dark:border-darkBorder p-8">
                        <h2 class="display-font text-2xl uppercase mb-6">Pembayaran</h2>
                        
                        @if($order->status == 'payment_uploaded')
                            <div class="mb-6 p-4 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200 text-[10px] tracking-widest uppercase border border-yellow-200 dark:border-yellow-800">
                                Bukti pembayaran sedang diverifikasi oleh admin.
                            </div>
                        @endif

                        @php $latestPayment = $order->payments->last(); @endphp
                        @if($latestPayment && $latestPayment->status == 'rejected')
                            <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 text-[10px] tracking-widest uppercase border border-red-200 dark:border-red-800">
                                Pembayaran Ditolak: {{ $latestPayment->admin_note }}
                            </div>
                        @endif

                        @if(isset($snapToken) && $snapToken)
                            <div class="mb-8 p-6 border-2 border-emerald-500 bg-emerald-50/10 dark:bg-emerald-950/20 text-center space-y-4 rounded-lg">
                                <h3 class="display-font text-xl uppercase text-emerald-600 dark:text-emerald-400">⚡ Pembayaran Instan via Midtrans Gateway</h3>
                                <p class="text-[10px] tracking-widest uppercase text-lightMuted dark:text-darkMuted">Bayar cepat & verifikasi otomatis via QRIS (GoPay/ShopeePay/OVO), Virtual Account Bank (BCA, Mandiri, BRI, BNI), atau Kartu Kredit</p>
                                <button id="pay-button" type="button" class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white text-xs tracking-[0.2em] uppercase font-bold transition-all shadow-md">
                                    Bayar Sekarang (Midtrans Gateway)
                                </button>
                            </div>
                        @endif

                        <div class="mb-8 space-y-4">
                            <h3 class="text-[10px] tracking-[0.2em] uppercase font-bold text-lightMuted dark:text-darkMuted">Metode Transfer Manual (Alternatif)</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @if(isset($paymentMethods))
                                    @foreach($paymentMethods as $method)
                                        <div class="border border-lightBorder dark:border-darkBorder p-4 text-[10px] tracking-widest uppercase">
                                            <p class="font-bold mb-1">{{ $method->bank_name ?? 'Metode' }}</p>
                                            <p>{{ $method->account_number }}</p>
                                            <p class="text-lightMuted dark:text-darkMuted">{{ $method->account_name }}</p>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <form action="{{ url('/pelanggan/pesanan/' . $order->id . '/upload-bukti') }}" method="POST" enctype="multipart/form-data" class="space-y-6 border-t-minimal border-lightBorder dark:border-darkBorder pt-6">
                            @csrf
                            <div>
                                <label class="block text-[10px] tracking-[0.2em] uppercase font-bold mb-2">Pilih Metode Pembayaran yang Digunakan</label>
                                <select name="payment_method_id" required class="w-full bg-transparent border border-lightBorder dark:border-darkBorder p-3 outline-none text-sm uppercase">
                                    <option value="" class="bg-lightBg dark:bg-darkBg">Pilih Metode</option>
                                    @if(isset($paymentMethods))
                                        @foreach($paymentMethods as $method)
                                            <option value="{{ $method->id }}" class="bg-lightBg dark:bg-darkBg">{{ $method->bank_name ?? 'Metode' }} - {{ $method->account_number }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-[10px] tracking-[0.2em] uppercase font-bold mb-2">Upload Bukti Transfer</label>
                                <input type="file" name="payment_proof" required accept="image/*,.pdf" class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:border-0 file:text-[10px] file:tracking-widest file:uppercase file:bg-lightMain dark:file:bg-darkMain file:text-lightBg dark:file:text-darkBg hover:file:opacity-90 transition-opacity">
                            </div>

                            <button type="submit" class="w-full py-3 bg-lightMain dark:bg-darkMain text-lightBg dark:text-darkBg text-center text-[10px] tracking-[0.2em] uppercase font-bold hover:opacity-90 transition-opacity">
                                Upload Bukti Pembayaran
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Payment History -->
                @if($order->payments && $order->payments->count() > 0)
                    <div>
                        <h2 class="display-font text-xl uppercase mb-4">Riwayat Pembayaran</h2>
                        <div class="space-y-3">
                            @foreach($order->payments as $payment)
                                <div class="flex justify-between items-center text-[10px] tracking-widest uppercase p-4 border border-lightBorder dark:border-darkBorder">
                                    <div>
                                        <p class="font-bold mb-1">{{ $payment->created_at->format('d M Y, H:i') }}</p>
                                        <p class="text-lightMuted dark:text-darkMuted">Rp{{ number_format($payment->amount, 0, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <span class="px-2 py-1 border border-{{ $payment->status == 'approved' ? 'green' : ($payment->status == 'rejected' ? 'red' : 'yellow') }}-500">
                                            {{ $payment->status }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <!-- Rating Section -->
                @if($order->status == 'completed')
                    <div class="border border-lightBorder dark:border-darkBorder p-8">
                        <h2 class="display-font text-2xl uppercase mb-6">Penilaian Pesanan</h2>
                        @if($order->rating)
                            <div class="space-y-4 text-sm">
                                <div class="flex text-yellow-500 text-lg">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $order->rating->rating)
                                            <span>★</span>
                                        @else
                                            <span class="text-gray-300 dark:text-gray-700">★</span>
                                        @endif
                                    @endfor
                                </div>
                                <p class="italic">"{{ $order->rating->review ?? 'Tidak ada ulasan.' }}"</p>
                                <p class="text-[10px] tracking-widest uppercase text-lightMuted dark:text-darkMuted">{{ $order->rating->created_at->format('d M Y') }}</p>
                            </div>
                        @else
                            <p class="text-[10px] tracking-widest uppercase text-lightMuted dark:text-darkMuted mb-4">Anda belum memberikan penilaian untuk pesanan ini.</p>
                            @if($order->rating_token)
                                <a href="{{ url('/rating/' . $order->rating_token) }}" class="inline-block px-6 py-2 border border-lightMain dark:border-darkMain text-[10px] tracking-widest uppercase font-bold hover:bg-lightMain dark:hover:bg-darkMain hover:text-lightBg dark:hover:text-darkBg transition-colors">
                                    Beri Penilaian
                                </a>
                            @endif
                        @endif
                    </div>
                @endif
            </div>

            <!-- Right: Order Summary & Info -->
            <div class="lg:col-span-1 space-y-8">
                <!-- Total Summary -->
                <div class="border border-lightBorder dark:border-darkBorder p-8 bg-lightBg dark:bg-darkBg">
                    <h2 class="display-font text-xl uppercase mb-6 pb-4 border-b-minimal border-lightBorder dark:border-darkBorder">Ringkasan</h2>
                    
                    <div class="space-y-4 text-[10px] tracking-widest uppercase mb-6">
                        <div class="flex justify-between">
                            <span class="text-lightMuted dark:text-darkMuted">Subtotal</span>
                            <span>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-lightMuted dark:text-darkMuted">Ongkos Kirim</span>
                            <span>{{ $order->shipping_fee > 0 ? 'Rp' . number_format($order->shipping_fee, 0, ',', '.') : 'Belum Dihitung' }}</span>
                        </div>
                        @if($order->additional_fee > 0)
                        <div class="flex justify-between">
                            <span class="text-lightMuted dark:text-darkMuted">Biaya Tambahan</span>
                            <span>Rp{{ number_format($order->additional_fee, 0, ',', '.') }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="flex justify-between items-center pt-4 border-t-minimal border-lightBorder dark:border-darkBorder font-bold text-sm tracking-widest uppercase mb-6">
                        <span>Total Akhir</span>
                        <span>Rp{{ number_format($order->grand_total, 0, ',', '.') }}</span>
                    </div>
                    
                    @if($order->status == 'shipped')
                        <form action="{{ url('/pelanggan/pesanan/' . $order->id . '/confirm') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-4 bg-lightMain dark:bg-darkMain text-lightBg dark:text-darkBg text-center text-[10px] tracking-[0.2em] uppercase font-bold hover:opacity-90 transition-opacity">
                                Konfirmasi Pesanan Diterima
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Shipping Info -->
                <div class="border border-lightBorder dark:border-darkBorder p-6">
                    <h3 class="display-font text-lg uppercase mb-4 border-b-minimal border-lightBorder dark:border-darkBorder pb-2">Info Pengiriman</h3>
                    <div class="space-y-3 text-[10px] tracking-widest uppercase">
                        <div>
                            <span class="block text-lightMuted dark:text-darkMuted mb-1">Penerima</span>
                            <span class="font-bold">{{ $order->shipping_name }}</span>
                        </div>
                        <div>
                            <span class="block text-lightMuted dark:text-darkMuted mb-1">Kontak</span>
                            <span>{{ $order->shipping_phone }}<br>{{ $order->shipping_email }}</span>
                        </div>
                        <div>
                            <span class="block text-lightMuted dark:text-darkMuted mb-1">Alamat</span>
                            <span class="leading-relaxed">{{ $order->shipping_address }}</span>
                        </div>
                        @if($order->tracking_number)
                        <div class="pt-3 border-t-minimal border-lightBorder dark:border-darkBorder mt-3">
                            <span class="block text-lightMuted dark:text-darkMuted mb-1">No. Resi</span>
                            <span class="font-bold text-sm">{{ $order->tracking_number }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script>
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;
        
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }

        if(themeToggle) {
            themeToggle.addEventListener('click', () => {
                html.classList.toggle('dark');
                if (html.classList.contains('dark')) {
                    localStorage.theme = 'dark';
                } else {
                    localStorage.theme = 'light';
                }
            });
        }
    </script>

    @if(isset($snapToken) && $snapToken && isset($midtransSnapUrl))
        <script src="{{ $midtransSnapUrl }}" data-client-key="{{ $midtransClientKey ?? '' }}"></script>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function () {
                const payButton = document.getElementById('pay-button');
                if (payButton) {
                    payButton.addEventListener('click', function (e) {
                        e.preventDefault();
                        window.snap.pay('{{ $snapToken }}', {
                            onSuccess: function (result) {
                                alert("Pembayaran berhasil!");
                                window.location.reload();
                            },
                            onPending: function (result) {
                                alert("Menunggu pembayaran...");
                                window.location.reload();
                            },
                            onError: function (result) {
                                alert("Pembayaran gagal!");
                            },
                            onClose: function () {
                                console.log('Midtrans Snap popup closed');
                            }
                        });
                    });
                }
            });
        </script>
    @endif
</body>
</html>



