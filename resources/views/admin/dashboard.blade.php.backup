@extends('admin.layout.main')

@section('title', 'Dashboard - Panel Admin')

@section('content')
<div class="mb-8">
    @if(isset($pending_orders) && $pending_orders > 0)
    <div class="bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 p-4 rounded mb-6 flex justify-between items-center border-minimal border-amber-200 dark:border-amber-800">
        <span class="font-semibold">Ada {{ $pending_orders }} pesanan baru menunggu konfirmasi!</span>
        <a href="{{ url('/admin/orders?status=pending_confirmation') }}" class="text-xs uppercase tracking-widest font-bold underline">Lihat</a>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="border-minimal border-lightBorder dark:border-darkBorder rounded p-6 bg-black/5 dark:bg-white/5">
            <h3 class="text-xs uppercase tracking-widest font-bold text-lightMuted dark:text-darkMuted mb-2">Total Users</h3>
            <p class="text-3xl display-font">{{ $total_users ?? 0 }}</p>
        </div>
        <div class="border-minimal border-lightBorder dark:border-darkBorder rounded p-6 bg-black/5 dark:bg-white/5">
            <h3 class="text-xs uppercase tracking-widest font-bold text-lightMuted dark:text-darkMuted mb-2">Total Products</h3>
            <p class="text-3xl display-font">{{ $total_products ?? 0 }}</p>
        </div>
        <div class="border-minimal border-lightBorder dark:border-darkBorder rounded p-6 bg-black/5 dark:bg-white/5">
            <h3 class="text-xs uppercase tracking-widest font-bold text-lightMuted dark:text-darkMuted mb-2">Total Pesanan</h3>
            <p class="text-3xl display-font">{{ $total_orders ?? 0 }}</p>
        </div>
        <div class="border-minimal border-lightBorder dark:border-darkBorder rounded p-6 bg-black/5 dark:bg-white/5">
            <h3 class="text-xs uppercase tracking-widest font-bold text-lightMuted dark:text-darkMuted mb-2">Pendapatan</h3>
            <p class="text-2xl font-mono mt-1 font-semibold">Rp{{ number_format($total_revenue ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>

    <div>
        <div class="flex justify-between items-end mb-4">
            <h3 class="display-font text-xl">Pesanan Terbaru</h3>
            <a href="{{ url('/admin/orders') }}" class="text-xs uppercase tracking-widest font-bold hover:underline">Lihat Semua</a>
        </div>
        
        <div class="border-minimal border-lightBorder dark:border-darkBorder rounded overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b-minimal border-lightBorder dark:border-darkBorder bg-black/5 dark:bg-white/5 uppercase tracking-widest text-[10px] font-bold">
                    <tr>
                        <th class="p-4">Invoice #</th>
                        <th class="p-4">Pelanggan</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Tanggal</th>
                        <th class="p-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-lightBorder dark:divide-darkBorder border-t border-lightBorder dark:border-darkBorder">
                    @forelse($recent_orders ?? [] as $order)
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
                        <tr class="hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                            <td class="p-4 font-mono">{{ $order->invoice_number ?? 'Belum ada' }}</td>
                            <td class="p-4">{{ $order->shipping_name }}</td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $badgeColor }}">
                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                </span>
                            </td>
                            <td class="p-4 text-xs">{{ $order->created_at->format('d M Y H:i') }}</td>
                            <td class="p-4">
                                <a href="{{ url('/admin/orders/' . $order->id) }}" class="text-xs uppercase tracking-widest font-bold hover:underline text-lightMain dark:text-darkMain">Lihat Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-lightMuted dark:text-darkMuted">Belum ada pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
