import React, { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import {
  createColumnHelper,
  flexRender,
  getCoreRowModel,
  useReactTable,
  getPaginationRowModel,
} from '@tanstack/react-table';
import { toast } from 'sonner';

type Product = {
  id: number;
  name: string;
  price: number;
  stock: number;
  category?: { name: string };
  status: string;
};

const columnHelper = createColumnHelper<Product>();

export default function ProductTable() {
  const [page, setPage] = useState(1);
  const [search, setSearch] = useState('');

  // Fetching data using TanStack Query
  const { data, isLoading, isError, refetch } = useQuery({
    queryKey: ['admin-products', page, search],
    queryFn: async () => {
      const res = await fetch(`/admin/api/products?page=${page}&search=${search}`);
      if (!res.ok) throw new Error('Network response was not ok');
      return res.json();
    }
  });

  const columns = [
    columnHelper.accessor('name', {
      header: 'Nama Produk',
      cell: info => <span className="font-medium">{info.getValue()}</span>,
    }),
    columnHelper.accessor('category.name', {
      header: 'Kategori',
      cell: info => info.getValue() || 'Uncategorized',
    }),
    columnHelper.accessor('price', {
      header: 'Harga',
      cell: info => `Rp ${Number(info.getValue()).toLocaleString('id-ID')}`,
    }),
    columnHelper.accessor('stock', {
      header: 'Stok',
      cell: info => (
        <span className={`px-2 py-1 rounded text-sm ${info.getValue() > 5 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
          {info.getValue()}
        </span>
      ),
    }),
    columnHelper.display({
      id: 'actions',
      header: 'Aksi',
      cell: (props) => (
        <div className="flex space-x-2">
          <button 
            className="text-blue-600 hover:underline"
            onClick={() => window.location.href = `/admin/products/edit/${props.row.original.id}`}
          >
            Edit
          </button>
          <button 
            className="text-red-600 hover:underline"
            onClick={async () => {
               if (confirm('Yakin hapus produk ini?')) {
                  const res = await fetch(`/admin/api/products/${props.row.original.id}`, {
                     method: 'DELETE',
                     headers: { 'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content }
                  });
                  if (res.ok) {
                     toast.success('Produk dihapus');
                     refetch();
                  } else {
                     toast.error('Gagal menghapus');
                  }
               }
            }}
          >
            Hapus
          </button>
        </div>
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
    <div className="bg-lightBg dark:bg-darkBg border border-lightBorder dark:border-darkBorder p-6 rounded-lg shadow-sm text-lightMain dark:text-darkMain">
      <div className="flex justify-between items-center mb-4">
        <input 
          type="text" 
          placeholder="Cari produk..." 
          className="border border-lightBorder dark:border-darkBorder bg-transparent rounded p-2 w-64 outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors"
          value={search}
          onChange={(e) => setSearch(e.target.value)}
        />
        <a href="/admin/products/create" className="bg-lightMain dark:bg-darkMain text-lightBg dark:text-darkBg px-4 py-2 rounded font-bold uppercase tracking-widest text-xs hover:opacity-90">Tambah Produk</a>
      </div>

      {isLoading ? (
        <div className="animate-pulse flex flex-col space-y-4">
           {[...Array(5)].map((_, i) => <div key={i} className="h-10 bg-black/10 dark:bg-white/10 rounded"></div>)}
        </div>
      ) : (
        <div className="overflow-x-auto">
          <table className="w-full text-left border-collapse">
            <thead>
              {table.getHeaderGroups().map(headerGroup => (
                <tr key={headerGroup.id} className="border-b border-lightBorder dark:border-darkBorder">
                  {headerGroup.headers.map(header => (
                    <th key={header.id} className="p-3 text-lightMuted dark:text-darkMuted text-xs font-bold uppercase tracking-widest">
                      {flexRender(header.column.columnDef.header, header.getContext())}
                    </th>
                  ))}
                </tr>
              ))}
            </thead>
            <tbody>
              {table.getRowModel().rows.map(row => (
                <tr key={row.id} className="border-b border-lightBorder dark:border-darkBorder hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                  {row.getVisibleCells().map(cell => (
                    <td key={cell.id} className="p-3 text-sm">
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
      <div className="flex items-center justify-between mt-4 text-sm font-semibold tracking-widest uppercase text-lightMuted dark:text-darkMuted">
        <button 
          onClick={() => setPage(p => Math.max(1, p - 1))} 
          disabled={page === 1}
          className="px-4 py-2 border border-lightBorder dark:border-darkBorder rounded disabled:opacity-50 hover:bg-black/5 dark:hover:bg-white/5 transition-colors"
        >
          Previous
        </button>
        <span>Page {page} of {data?.last_page || 1}</span>
        <button 
          onClick={() => setPage(p => p + 1)} 
          disabled={page >= (data?.last_page || 1)}
          className="px-4 py-2 border border-lightBorder dark:border-darkBorder rounded disabled:opacity-50 hover:bg-black/5 dark:hover:bg-white/5 transition-colors"
        >
          Next
        </button>
      </div>
    </div>
  );
}
