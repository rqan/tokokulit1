@extends('admin.layout.main')

@section('title', 'Manajemen Pesanan - Panel Admin')

@section('content')
<div class="mb-8">
    <div class="flex flex-wrap gap-2 mb-6">
        @php
            $statuses = [
                'Semua' => '',
                'Menunggu Konfirmasi' => 'pending_confirmation',
                'Menunggu Pembayaran' => 'awaiting_payment',
                'Pembayaran Terupload' => 'payment_uploaded',
                'Diproses' => 'processing',
                'Dikirim' => 'shipped',
                'Selesai' => 'completed',
                'Dibatalkan' => 'cancelled'
            ];
            $currentStatus = request('status');
        @endphp
        
        @foreach($statuses as $label => $val)
            <a href="?status={{ $val }}" 
               class="px-4 py-2 border-minimal border-lightBorder dark:border-darkBorder rounded text-xs uppercase tracking-widest font-bold {{ $currentStatus == $val ? 'bg-lightMain dark:bg-darkMain text-lightBg dark:text-darkBg' : 'hover:bg-black/5 dark:hover:bg-white/5' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="border-minimal border-lightBorder dark:border-darkBorder rounded overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b-minimal border-lightBorder dark:border-darkBorder bg-black/5 dark:bg-white/5 uppercase tracking-widest text-[10px] font-bold">
                <tr>
                    <th class="p-4">Invoice #</th>
                    <th class="p-4">Pelanggan</th>
                    <th class="p-4">Total</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Tanggal</th>
                    <th class="p-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-lightBorder dark:divide-darkBorder border-t border-lightBorder dark:border-darkBorder">
                @forelse($orders as $order)
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
                        <td class="p-4">{{ $order->shipping_name }}<br><span class="text-xs text-lightMuted dark:text-darkMuted">{{ $order->shipping_email }}</span></td>
                        <td class="p-4 font-mono">Rp{{ number_format($order->grand_total, 0, ',', '.') }}</td>
                        <td class="p-4">
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $badgeColor }}">
                                {{ $statusLabels[$order->status] ?? $order->status }}
                            </span>
                        </td>
                        <td class="p-4 text-xs text-lightMuted dark:text-darkMuted">{{ $order->created_at->format('d M Y H:i') }}</td>
                        <td class="p-4">
                            <a href="{{ url('/admin/orders/' . $order->id) }}" class="text-xs uppercase tracking-widest font-bold hover:underline text-lightMain dark:text-darkMain">Lihat Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-lightMuted dark:text-darkMuted">Tidak ada pesanan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-6">
        
    </div>
</div>
@endsection
