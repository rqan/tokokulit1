import React, { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import {
  createColumnHelper,
  flexRender,
  getCoreRowModel,
  useReactTable,
  getPaginationRowModel,
} from '@tanstack/react-table';
import { toast } from 'sonner';

type Order = {
  id: number;
  order_number: string;
  total_amount: number;
  status: string;
  tracking_number?: string;
  created_at: string;
  user?: {
    name: string;
    email: string;
  };
};

const columnHelper = createColumnHelper<Order>();

export default function OrderTable() {
  const queryClient = useQueryClient();
  const [page, setPage] = useState(1);
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState('all');

  const { data, isLoading } = useQuery({
    queryKey: ['admin-orders', page, search, statusFilter],
    queryFn: async () => {
      const res = await fetch(`/admin/api/orders?page=${page}&search=${search}&status=${statusFilter}`);
      if (!res.ok) throw new Error('Network response was not ok');
      return res.json();
    }
  });

  const updateStatusMutation = useMutation({
    mutationFn: async ({ id, status, tracking_number }: { id: number, status: string, tracking_number?: string }) => {
      const res = await fetch(`/admin/api/orders/${id}/status`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
        },
        body: JSON.stringify({ status, tracking_number })
      });
      if (!res.ok) throw new Error('Gagal update status');
      return res.json();
    },
    onSuccess: () => {
      toast.success('Status pesanan diperbarui');
      queryClient.invalidateQueries({ queryKey: ['admin-orders'] });
    },
    onError: () => {
      toast.error('Gagal memperbarui status');
    }
  });

  const columns = [
    columnHelper.accessor('order_number', {
      header: 'ID Pesanan',
      cell: info => <span className="font-mono font-medium">{info.getValue()}</span>,
    }),
    columnHelper.accessor('created_at', {
      header: 'Tanggal',
      cell: info => new Date(info.getValue()).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }),
    }),
    columnHelper.accessor('user.name', {
      header: 'Pelanggan',
      cell: info => (
        <div>
          <p className="font-medium">{info.getValue() || 'Guest'}</p>
          <p className="text-xs text-gray-500">{info.row.original.user?.email}</p>
        </div>
      ),
    }),
    columnHelper.accessor('total_amount', {
      header: 'Total',
      cell: info => `Rp ${Number(info.getValue()).toLocaleString('id-ID')}`,
    }),
    columnHelper.accessor('status', {
      header: 'Status',
      cell: info => {
        const val = info.getValue();
        const colors: Record<string, string> = {
          pending_confirmation: 'bg-amber-100 text-amber-800',
          awaiting_payment: 'bg-blue-100 text-blue-800',
          payment_uploaded: 'bg-purple-100 text-purple-800',
          processing: 'bg-indigo-100 text-indigo-800',
          shipped: 'bg-cyan-100 text-cyan-800',
          completed: 'bg-green-100 text-green-800',
          cancelled: 'bg-red-100 text-red-800',
        };
        const labels: Record<string, string> = {
            pending_confirmation: 'Menunggu Konfirmasi',
            awaiting_payment: 'Menunggu Pembayaran',
            payment_uploaded: 'Pembayaran Terupload',
            processing: 'Diproses',
            shipped: 'Dikirim',
            completed: 'Selesai',
            cancelled: 'Dibatalkan'
        };
        return (
          <select 
            className={`text-xs font-semibold rounded px-2 py-1 outline-none cursor-pointer border-r-8 border-transparent ${colors[val] || 'bg-gray-100 text-gray-800'}`}
            value={val}
            onChange={(e) => {
              const newStatus = e.target.value;
              let tracking = info.row.original.tracking_number;
              if (newStatus === 'shipped' && !tracking) {
                tracking = prompt('Masukkan resi pengiriman:') || undefined;
                if (!tracking) return; // user cancelled
              }
              updateStatusMutation.mutate({ id: info.row.original.id, status: newStatus, tracking_number: tracking });
            }}
          >
            {Object.entries(labels).map(([key, label]) => (
                <option key={key} value={key}>{label}</option>
            ))}
          </select>
        );
      },
    }),
    columnHelper.display({
      id: 'actions',
      header: 'Aksi',
      cell: (props) => (
        <a 
          href={`/admin/orders/${props.row.original.id}`}
          className="text-indigo-600 hover:underline text-sm font-medium"
        >
          Detail
        </a>
      ),
    }),
  ];

  const table = useReactTable({
    data: data?.data ?? [],
    columns,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    manualPagination: true,
    pageCount: data?.last_page ?? -1,
  });

  return (
    <div className="bg-lightBg dark:bg-darkBg p-6 rounded-xl border border-lightBorder dark:border-darkBorder shadow-sm space-y-4 text-lightMain dark:text-darkMain">
      {/* Filters */}
      <div className="flex flex-col md:flex-row justify-between gap-4">
        <input 
          type="text" 
          placeholder="Cari ID Pesanan, Nama, Email..." 
          className="border border-lightBorder dark:border-darkBorder bg-transparent rounded-md px-3 py-2 w-full md:w-80 outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors"
          value={search}
          onChange={(e) => setSearch(e.target.value)}
        />
        
        <select 
          className="border border-lightBorder dark:border-darkBorder bg-transparent rounded-md px-3 py-2 outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors"
          value={statusFilter}
          onChange={(e) => setStatusFilter(e.target.value)}
        >
          <option value="all" className="bg-lightBg dark:bg-darkBg">Semua Status</option>
          <option value="pending_confirmation" className="bg-lightBg dark:bg-darkBg">Menunggu Konfirmasi</option>
          <option value="awaiting_payment" className="bg-lightBg dark:bg-darkBg">Menunggu Pembayaran</option>
          <option value="payment_uploaded" className="bg-lightBg dark:bg-darkBg">Pembayaran Terupload</option>
          <option value="processing" className="bg-lightBg dark:bg-darkBg">Diproses</option>
          <option value="shipped" className="bg-lightBg dark:bg-darkBg">Dikirim</option>
          <option value="completed" className="bg-lightBg dark:bg-darkBg">Selesai</option>
          <option value="cancelled" className="bg-lightBg dark:bg-darkBg">Dibatalkan</option>
        </select>
      </div>

      {isLoading ? (
        <div className="animate-pulse flex flex-col space-y-4 pt-4">
           {[...Array(6)].map((_, i) => <div key={i} className="h-12 bg-gray-200 dark:bg-gray-800 rounded-md"></div>)}
        </div>
      ) : (
        <div className="overflow-x-auto rounded-lg border border-lightBorder dark:border-darkBorder">
          <table className="w-full text-left border-collapse">
            <thead>
              {table.getHeaderGroups().map(headerGroup => (
                <tr key={headerGroup.id} className="bg-black/5 dark:bg-white/5 border-b border-lightBorder dark:border-darkBorder">
                  {headerGroup.headers.map(header => (
                    <th key={header.id} className="p-4 text-xs text-lightMuted dark:text-darkMuted font-bold uppercase tracking-widest">
                      {flexRender(header.column.columnDef.header, header.getContext())}
                    </th>
                  ))}
                </tr>
              ))}
            </thead>
            <tbody>
              {table.getRowModel().rows.length === 0 ? (
                <tr>
                   <td colSpan={6} className="p-8 text-center text-lightMuted dark:text-darkMuted">Tidak ada data pesanan.</td>
                </tr>
              ) : table.getRowModel().rows.map(row => (
                <tr key={row.id} className="border-b border-lightBorder dark:border-darkBorder hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                  {row.getVisibleCells().map(cell => (
                    <td key={cell.id} className="p-4 text-sm">
                      {flexRender(cell.column.columnDef.cell, cell.getContext())}
                    </td>
                  ))}
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      {/* Pagination */}
      <div className="flex items-center justify-between pt-2">
        <span className="text-sm font-semibold tracking-widest uppercase text-lightMuted dark:text-darkMuted">
            Halaman {page} dari {data?.last_page || 1}
        </span>
        <div className="flex space-x-2">
            <button 
                onClick={() => setPage(p => Math.max(1, p - 1))} 
                disabled={page === 1}
                className="px-3 py-1.5 border border-lightBorder dark:border-darkBorder rounded-md text-sm font-bold uppercase tracking-widest hover:bg-black/5 dark:hover:bg-white/5 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
                Sebelumnya
            </button>
            <button 
                onClick={() => setPage(p => p + 1)} 
                disabled={page >= (data?.last_page || 1)}
                className="px-3 py-1.5 border border-lightBorder dark:border-darkBorder rounded-md text-sm font-bold uppercase tracking-widest hover:bg-black/5 dark:hover:bg-white/5 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
                Selanjutnya
            </button>
        </div>
      </div>
    </div>
  );
}
