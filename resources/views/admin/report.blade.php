<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan & Penjualan — Admin Eny Leather</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-slate-100 p-8 antialiased">
    <div class="max-w-7xl mx-auto space-y-8">
        
        <header class="flex justify-between items-center border-b border-slate-800 pb-6">
            <div>
                <h1 class="text-3xl font-bold uppercase tracking-wider text-amber-500">Laporan Penjualan & Keuangan</h1>
                <p class="text-xs text-slate-400 mt-1">Rekapitulasi transaksi komersial dan ekspor data penjualan.</p>
            </div>
            <div class="flex gap-4">
                <a href="{{ url('/admin/dashboard') }}" class="px-4 py-2 border border-slate-700 text-xs font-bold uppercase tracking-widest hover:bg-slate-800 transition-colors">
                    ← Kembali ke Dashboard
                </a>
                <a href="{{ route('admin.reports.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold uppercase tracking-widest rounded transition-colors shadow">
                    📥 Ekspor ke Excel/CSV
                </a>
            </div>
        </header>

        <!-- Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-slate-900 border border-slate-800 p-6 rounded-xl space-y-2">
                <p class="text-xs uppercase font-bold text-slate-400">Total Pesanan</p>
                <h3 class="text-3xl font-extrabold text-white">{{ $totalOrders }} <span class="text-xs font-normal text-slate-500">Transaksi</span></h3>
            </div>
            <div class="bg-slate-900 border border-slate-800 p-6 rounded-xl space-y-2">
                <p class="text-xs uppercase font-bold text-slate-400">Pesanan Lunas (Success)</p>
                <h3 class="text-3xl font-extrabold text-emerald-400">{{ $completedOrders }} <span class="text-xs font-normal text-slate-500">Pesanan</span></h3>
            </div>
            <div class="bg-slate-900 border border-slate-800 p-6 rounded-xl space-y-2">
                <p class="text-xs uppercase font-bold text-slate-400">Total Omset Penjualan</p>
                <h3 class="text-3xl font-extrabold text-amber-400">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            </div>
        </div>

        <!-- Date Filter Form -->
        <form action="{{ route('admin.reports.index') }}" method="GET" class="bg-slate-900 border border-slate-800 p-4 rounded-xl flex flex-wrap gap-4 items-end text-xs">
            <div>
                <label class="block uppercase font-bold text-slate-400 mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="bg-slate-950 border border-slate-700 p-2 rounded text-white">
            </div>
            <div>
                <label class="block uppercase font-bold text-slate-400 mb-1">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="bg-slate-950 border border-slate-700 p-2 rounded text-white">
            </div>
            <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold uppercase tracking-widest rounded transition-colors">
                Filter Laporan
            </button>
        </form>

        <!-- Orders Table -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden shadow">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950 text-slate-400 uppercase tracking-widest border-b border-slate-800">
                    <tr>
                        <th class="p-4">No Invoice</th>
                        <th class="p-4">Tanggal</th>
                        <th class="p-4">Pelanggan</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Grand Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-800/50">
                            <td class="p-4 font-mono font-bold text-amber-400">{{ $order->invoice_number ?? "ORDER-{$order->id}" }}</td>
                            <td class="p-4">{{ $order->created_at->format('d M Y, H:i') }}</td>
                            <td class="p-4">
                                <p class="font-bold text-white">{{ $order->shipping_name }}</p>
                                <p class="text-[10px] text-slate-400">{{ $order->shipping_email }}</p>
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded text-[10px] uppercase font-bold bg-slate-800 text-slate-300">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="p-4 text-right font-bold text-white">Rp{{ number_format($order->grand_total ?? $order->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-500 uppercase tracking-widest">Tidak ada data transaksi pada rentang tanggal ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>
