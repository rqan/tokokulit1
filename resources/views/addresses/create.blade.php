<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Alamat - TOKO RAFI</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind-config.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ux.css') }}">
</head>
<body class="w-full relative antialiased selection:bg-lightMain selection:text-lightBg dark:selection:bg-darkMain dark:selection:text-darkBg bg-lightBg text-lightMain dark:bg-darkBg dark:text-darkMain">

    <header class="w-full px-8 py-6 flex justify-between items-center border-b-minimal border-lightBorder dark:border-darkBorder sticky top-0 z-40 bg-lightBg dark:bg-darkBg transition-colors">
        <div class="flex-1 flex justify-start">
            <a href="{{ url('/') }}" class="display-font text-4xl tracking-tight">TOKO RAFI</a>
        </div>
        <div class="flex-1 flex justify-end items-center space-x-6 md:space-x-8 text-[10px] font-semibold tracking-[0.2em] uppercase">
            <a href="{{ route('addresses.index') }}" class="link-hover">Buku Alamat</a>
            <a href="{{ url('/profile') }}" class="link-hover">Profile</a>
        </div>
    </header>

    <main class="w-full max-w-2xl mx-auto min-h-[70vh] px-8 py-16">
        <h1 class="display-font text-4xl uppercase mb-8">Tambah Alamat Baru</h1>

        @if($errors->any())
            <div class="p-4 mb-8 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('addresses.store') }}" method="POST" class="space-y-6 border border-lightBorder dark:border-darkBorder p-8">
            @csrf

            <div class="flex flex-col space-y-2">
                <label for="label" class="text-xs uppercase tracking-widest font-semibold">Label Alamat (Opsional)</label>
                <input type="text" name="label" id="label" placeholder="Contoh: Rumah, Kantor" class="w-full bg-transparent border border-lightBorder dark:border-darkBorder px-4 py-3 text-sm focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors" value="{{ old('label') }}">
            </div>

            <div class="flex flex-col space-y-2">
                <label for="full_address" class="text-xs uppercase tracking-widest font-semibold">Alamat Lengkap</label>
                <textarea name="full_address" id="full_address" rows="3" required class="w-full bg-transparent border border-lightBorder dark:border-darkBorder px-4 py-3 text-sm focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors">{{ old('full_address') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex flex-col space-y-2">
                    <label for="city" class="text-xs uppercase tracking-widest font-semibold">Kota</label>
                    <input type="text" name="city" id="city" required class="w-full bg-transparent border border-lightBorder dark:border-darkBorder px-4 py-3 text-sm focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors" value="{{ old('city') }}">
                </div>
                <div class="flex flex-col space-y-2">
                    <label for="postal_code" class="text-xs uppercase tracking-widest font-semibold">Kode Pos</label>
                    <input type="text" name="postal_code" id="postal_code" class="w-full bg-transparent border border-lightBorder dark:border-darkBorder px-4 py-3 text-sm focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors" value="{{ old('postal_code') }}">
                </div>
            </div>

            <div class="flex items-center space-x-3 pt-4">
                <input type="checkbox" name="is_primary" id="is_primary" value="1" class="w-4 h-4 bg-transparent border-lightBorder" {{ old('is_primary') ? 'checked' : '' }}>
                <label for="is_primary" class="text-xs uppercase tracking-widest font-semibold cursor-pointer">Jadikan Alamat Utama</label>
            </div>

            <div class="pt-6 border-t border-lightBorder dark:border-darkBorder flex justify-end space-x-4">
                <a href="{{ route('addresses.index') }}" class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-lightSecondary hover:text-lightMain dark:text-darkSecondary dark:hover:text-darkMain transition-colors">Batal</a>
                <button type="submit" class="px-8 py-4 bg-lightMain text-lightBg dark:bg-darkMain dark:text-darkBg text-xs font-bold uppercase tracking-widest hover:opacity-90 transition-opacity">Simpan Alamat</button>
            </div>
        </form>
    </main>
</body>
</html>


