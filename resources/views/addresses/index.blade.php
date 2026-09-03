<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Alamat - ENY LEATHER</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind-config.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ux.css') }}">
</head>
<body class="w-full relative antialiased selection:bg-lightMain selection:text-lightBg dark:selection:bg-darkMain dark:selection:text-darkBg bg-lightBg text-lightMain dark:bg-darkBg dark:text-darkMain">

    <header class="w-full px-8 py-6 flex justify-between items-center border-b-minimal border-lightBorder dark:border-darkBorder sticky top-0 z-40 bg-lightBg dark:bg-darkBg transition-colors">
        <div class="flex-1 flex justify-start">
            <a href="{{ url('/') }}" class="display-font text-4xl tracking-tight">ENY LEATHER</a>
        </div>
        <div class="flex-1 flex justify-end items-center space-x-6 md:space-x-8 text-[10px] font-semibold tracking-[0.2em] uppercase">
            <a href="{{ url('/katalog') }}" class="link-hover">Katalog</a>
            <a href="{{ url('/pelanggan/cart') }}" class="link-hover">Cart</a>
            <a href="{{ url('/profile') }}" class="link-hover">Profile</a>
        </div>
    </header>

    <main class="w-full max-w-5xl mx-auto min-h-[70vh] px-8 py-16">
        <div class="flex justify-between items-center mb-12">
            <h1 class="display-font text-4xl uppercase">Daftar Alamat</h1>
            <a href="{{ route('addresses.create') }}" class="px-6 py-3 border border-lightMain dark:border-darkMain text-xs font-bold uppercase tracking-widest hover:bg-lightMain hover:text-lightBg dark:hover:bg-darkMain dark:hover:text-darkBg transition-colors">Tambah Alamat</a>
        </div>

        @if(session('success'))
            <div class="p-4 mb-8 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if($addresses->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($addresses as $address)
                    <div class="border border-lightBorder dark:border-darkBorder p-6 relative {{ $address->is_primary ? 'ring-1 ring-lightMain dark:ring-darkMain' : '' }}">
                        @if($address->is_primary)
                            <span class="absolute top-4 right-4 bg-lightMain text-lightBg dark:bg-darkMain dark:text-darkBg text-[9px] px-2 py-1 uppercase tracking-widest font-bold">Utama</span>
                        @endif
                        <h3 class="text-sm font-bold uppercase tracking-widest mb-2">{{ $address->label ?? 'Alamat' }}</h3>
                        <p class="text-xs text-lightSecondary dark:text-darkSecondary mb-1">{{ $address->full_address }}</p>
                        <p class="text-xs text-lightSecondary dark:text-darkSecondary mb-4">{{ $address->city }} {{ $address->postal_code ? ', ' . $address->postal_code : '' }}</p>

                        <div class="flex items-center space-x-4 border-t border-lightBorder dark:border-darkBorder pt-4 mt-4">
                            <a href="{{ route('addresses.edit', $address->id) }}" class="text-[10px] uppercase tracking-widest font-bold hover:underline">Edit</a>
                            
                            <form action="{{ route('addresses.destroy', $address->id) }}" method="POST" onsubmit="return confirm('Hapus alamat ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-[10px] text-red-500 uppercase tracking-widest font-bold hover:underline">Hapus</button>
                            </form>

                            @if(!$address->is_primary)
                                <form action="{{ route('addresses.primary', $address->id) }}" method="POST" class="ml-auto">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-[10px] uppercase tracking-widest font-bold border border-lightMain dark:border-darkMain px-3 py-1 hover:bg-lightMain hover:text-lightBg dark:hover:bg-darkMain dark:hover:text-darkBg transition-colors">Jadikan Utama</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-20 border border-lightBorder dark:border-darkBorder">
                <p class="text-sm tracking-widest uppercase mb-4 text-lightSecondary dark:text-darkSecondary">Belum ada alamat yang tersimpan.</p>
                <a href="{{ route('addresses.create') }}" class="text-xs underline tracking-widest font-bold uppercase">Tambah Alamat Pertama Anda</a>
            </div>
        @endif
    </main>

</body>
</html>
