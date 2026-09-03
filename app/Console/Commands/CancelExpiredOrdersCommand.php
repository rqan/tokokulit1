<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CancelExpiredOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-expired {--hours=24 : Batas jam kedaluwarsa pesanan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membatalkan pesanan yang belum dibayar setelah melewati batas waktu dan mengembalikan stok produk.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $cutoff = now()->subHours($hours);

        $expiredOrders = Order::whereIn('status', [Order::STATUS_PENDING, Order::STATUS_AWAITING_PAYMENT])
            ->where('created_at', '<=', $cutoff)
            ->get();

        if ($expiredOrders->isEmpty()) {
            $this->info("Tidak ada pesanan kedaluwarsa (pembatalan > {$hours} jam).");
            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($expiredOrders as $order) {
            $order->update([
                'status' => Order::STATUS_CANCELLED,
            ]);

            // Restock items
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                }
            }

            Log::info("Order #{$order->id} ({$order->invoice_number}) otomatis dibatalkan karena kedaluwarsa > {$hours} jam.");
            $count++;
        }

        $this->info("Berhasil membatalkan {$count} pesanan kedaluwarsa dan mengembalikan stok.");
        return Command::SUCCESS;
    }
}
