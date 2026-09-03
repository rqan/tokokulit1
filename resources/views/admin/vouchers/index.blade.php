<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kupon & Voucher — Admin Eny Leather</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-slate-100 p-8 antialiased">
    <div class="max-w-6xl mx-auto space-y-8">
        
        <header class="flex justify-between items-center border-b border-slate-800 pb-6">
            <div>
                <h1 class="text-3xl font-bold uppercase tracking-wider text-amber-500">Kelola Kupon Diskon & Voucher</h1>
                <p class="text-xs text-slate-400 mt-1">Buat kode promo, diskon persentase, dan nominal tetap untuk pelanggan.</p>
            </div>
            <a href="{{ url('/admin/dashboard') }}" class="px-4 py-2 border border-slate-700 text-xs font-bold uppercase tracking-widest hover:bg-slate-800 transition-colors">
                ← Kembali ke Dashboard
            </a>
        </header>

        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500 text-emerald-400 text-xs rounded uppercase font-bold tracking-widest">
                {{ session('success') }}
            </div>
        @endif

        <!-- Form Tambah Voucher -->
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-xl space-y-4">
            <h2 class="text-lg font-bold uppercase text-white tracking-wider">Tambah Voucher Baru</h2>
            <form action="{{ route('admin.vouchers.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
                @csrf
                <div>
                    <label class="block uppercase font-bold text-slate-400 mb-1">Kode Voucher</label>
                    <input type="text" name="code" required placeholder="MISAL: ENY LEATHER50" class="w-full bg-slate-950 border border-slate-700 p-2.5 rounded text-white uppercase font-mono">
                </div>
                <div>
                    <label class="block uppercase font-bold text-slate-400 mb-1">Tipe Diskon</label>
                    <select name="type" required class="w-full bg-slate-950 border border-slate-700 p-2.5 rounded text-white uppercase">
                        <option value="fixed">Nominal Tetap (Rp)</option>
                        <option value="percent">Persentase (%)</option>
                    </select>
                </div>
                <div>
                    <label class="block uppercase font-bold text-slate-400 mb-1">Nilai Diskon</label>
                    <input type="number" name="amount" required placeholder="50000 atau 10" class="w-full bg-slate-950 border border-slate-700 p-2.5 rounded text-white">
                </div>
                <div>
                    <label class="block uppercase font-bold text-slate-400 mb-1">Minimal Pembelian (Rp)</label>
                    <input type="number" name="min_spend" placeholder="0" class="w-full bg-slate-950 border border-slate-700 p-2.5 rounded text-white">
                </div>
                <div class="md:col-span-4 flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-amber-500 hover:bg-amber-600 font-bold text-xs uppercase tracking-widest text-slate-950 rounded transition-colors">
                        + Simpan Voucher
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Voucher List -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden shadow">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950 text-slate-400 uppercase tracking-widest border-b border-slate-800">
                    <tr>
                        <th class="p-4">Kode Voucher</th>
                        <th class="p-4">Tipe & Diskon</th>
                        <th class="p-4">Min. Pembelian</th>
                        <th class="p-4">Batas Penggunaan</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($vouchers as $voucher)
                        <tr class="hover:bg-slate-800/50">
                            <td class="p-4 font-mono font-bold text-amber-400">{{ $voucher->code }}</td>
                            <td class="p-4 font-bold text-white">
                                {{ $voucher->type == 'fixed' ? 'Rp' . number_format($voucher->amount, 0, ',', '.') : $voucher->amount . '%' }}
                            </td>
                            <td class="p-4">Rp{{ number_format($voucher->min_spend, 0, ',', '.') }}</td>
                            <td class="p-4">{{ $voucher->used_count }} / {{ $voucher->usage_limit ?? 'Tak Terbatas' }}</td>
                            <td class="p-4 text-center">
                                <form action="{{ route('admin.vouchers.destroy', $voucher->id) }}" method="POST" onsubmit="return confirm('Hapus voucher ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:underline text-[10px] uppercase font-bold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-500 uppercase tracking-widest">Belum ada kupon diskon yang dibuat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>
