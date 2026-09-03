<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind-config.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ux.css') }}">
    <script src="{{ asset('js/ux.js') }}"></script>
</head>
<body class="w-full relative antialiased selection:bg-lightMain selection:text-lightBg dark:selection:bg-darkMain dark:selection:text-darkBg bg-lightBg text-lightMain dark:bg-darkBg dark:text-darkMain">

    

    <header class="w-full px-8 py-6 flex justify-between items-center border-b-minimal border-lightBorder dark:border-darkBorder sticky top-0 z-40 bg-lightBg dark:bg-darkBg transition-colors">
        <div class="flex-1 flex justify-start">
            <a href="{{ url('/') }}" class="display-font text-4xl tracking-tight">TOKO RAFI</a>
        </div>
        <div class="flex-1 flex justify-end items-center space-x-8 text-[10px] font-semibold tracking-[0.2em] uppercase">
            <button id="themeToggle" class="link-hover uppercase focus:outline-none">Theme</button>
            <a href="{{ url('/pelanggan/cart') }}" class="link-hover">Kembali ke Keranjang</a>
        </div>
    </header>

    <main class="w-full min-h-[70vh] px-8 py-16">
        <h1 class="display-font text-5xl md:text-6xl mb-12 uppercase">Checkout</h1>

        @if($errors->any())
            <div class="mb-8 p-4 border border-red-500 text-red-500 text-xs tracking-widest uppercase">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ url('/pelanggan/checkout/process') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-2 gap-16">
            @csrf
            
            <!-- Left: Form -->
            <div class="space-y-8">
                <h2 class="display-font text-2xl uppercase border-b-minimal border-lightBorder dark:border-darkBorder pb-4">Informasi Pengiriman</h2>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] tracking-[0.2em] uppercase font-bold mb-2">Nama Lengkap</label>
                        <input type="text" name="shipping_name" value="{{ old('shipping_name', Auth::check() ? Auth::user()->name : '') }}" required class="w-full bg-transparent border-b border-lightBorder dark:border-darkBorder py-2 outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] tracking-[0.2em] uppercase font-bold mb-2">Email</label>
                            <input type="email" name="shipping_email" value="{{ old('shipping_email', Auth::check() ? Auth::user()->email : '') }}" required class="w-full bg-transparent border-b border-lightBorder dark:border-darkBorder py-2 outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] tracking-[0.2em] uppercase font-bold mb-2">Nomor Telepon</label>
                            <input type="text" name="shipping_phone" value="{{ old('shipping_phone', Auth::check() ? Auth::user()->phone : '') }}" required class="w-full bg-transparent border-b border-lightBorder dark:border-darkBorder py-2 outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] tracking-[0.2em] uppercase font-bold mb-2">Alamat Pengiriman Lengkap</label>
                        <textarea name="shipping_address" rows="4" required class="w-full bg-transparent border border-lightBorder dark:border-darkBorder p-3 outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm resize-none">{{ old('shipping_address') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-[10px] tracking-[0.2em] uppercase font-bold mb-2">Catatan Pesanan (Opsional)</label>
                        <textarea name="notes" rows="2" class="w-full bg-transparent border border-lightBorder dark:border-darkBorder p-3 outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors text-sm resize-none">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Right: Order Summary -->
            <div>
                <div class="bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder p-8 sticky top-32">
                    <h2 class="display-font text-2xl uppercase mb-6">Pesanan Anda</h2>
                    
                    @php $total = 0; @endphp
                    @if(!empty($cart))
                        <div class="space-y-4 mb-6 pb-6 border-b-minimal border-lightBorder dark:border-darkBorder">
                            @foreach($cart as $key => $details)
                                @php $total += $details['product']['price'] * $details['quantity']; @endphp
                                <div class="flex justify-between text-sm">
                                    <div class="flex-1 pr-4">
                                        <span class="block uppercase">{{ $details['product']['name'] }}</span>
                                        @if(!empty($details['size']))
                                        <span class="block text-[10px] tracking-widest text-lightMuted dark:text-darkMuted">Ukuran: {{ $details['size'] }}</span>
                                        @endif
                                        <span class="text-[10px] tracking-widest text-lightMuted dark:text-darkMuted">QTY: {{ $details['quantity'] }}</span>
                                    </div>
                                    <span class="tracking-widest">Rp{{ number_format($details['product']['price'] * $details['quantity'], 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="space-y-4 mb-6 text-sm tracking-widest uppercase">
                        <div class="flex justify-between text-lightMuted dark:text-darkMuted">
                            <span>Subtotal</span>
                            <span>Rp{{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-lightMuted dark:text-darkMuted text-[10px]">
                            <span>Ongkos Kirim</span>
                            <span>Dihitung Nanti</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center py-6 border-t-minimal border-lightBorder dark:border-darkBorder font-bold text-lg tracking-widest uppercase mb-6">
                        <span>Total</span>
                        <span>Rp{{ number_format($total, 0, ',', '.') }}</span>
                    </div>

                    <div class="mb-8 p-4 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200 text-[10px] tracking-widest uppercase leading-relaxed text-center">
                        Ongkos kirim akan dihitung oleh admin setelah Anda membuat pesanan. Pembayaran dilakukan setelah ongkos kirim ditambahkan.
                    </div>

                    @foreach($selectedKeys as $sk)
                    <input type="hidden" name="selected_items[]" value="{{ $sk }}">
                    @endforeach
                    <button type="submit" class="w-full py-4 bg-lightMain dark:bg-darkMain text-lightBg dark:text-darkBg text-center text-[10px] tracking-[0.2em] uppercase font-bold hover:opacity-90 transition-opacity">
                        Buat Pesanan
                    </button>
                </div>
            </div>
        </form>
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
</body>
</html>





