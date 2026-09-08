import React, { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { createColumnHelper, flexRender, getCoreRowModel, useReactTable, getPaginationRowModel } from '@tanstack/react-table';
import { toast } from 'sonner';

type Subscriber = { id: number; email: string; created_at: string; };
type Campaign = { id: number; subject: string; content: string; status: string; sent_at: string; creator?: { name: string } };

const subHelper = createColumnHelper<Subscriber>();
const campHelper = createColumnHelper<Campaign>();

export default function NewsletterTable() {
  const queryClient = useQueryClient();
  const [activeTab, setActiveTab] = useState<'subscribers' | 'campaigns'>('subscribers');
  
  // Subscribers State
  const [page, setPage] = useState(1);
  const [search, setSearch] = useState('');

  // Campaigns State
  const [showForm, setShowForm] = useState(false);
  const [subject, setSubject] = useState('');
  const [content, setContent] = useState('');

  // Queries
  const { data: subData, isLoading: subLoading } = useQuery({
    queryKey: ['admin-newsletter', page, search],
    queryFn: async () => {
      const res = await fetch(`/admin/api/newsletter?page=${page}&search=${search}`);
      if (!res.ok) throw new Error('Network error');
      return res.json();
    }
  });

  const { data: campData, isLoading: campLoading } = useQuery({
    queryKey: ['admin-campaigns'],
    queryFn: async () => {
      const res = await fetch(`/admin/api/newsletter/campaigns`);
      if (!res.ok) throw new Error('Network error');
      return res.json();
    }
  });

  // Mutations
  const createCampaign = useMutation({
    mutationFn: async (data: {subject: string, content: string}) => {
      const res = await fetch(`/admin/api/newsletter/campaigns`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
        },
        body: JSON.stringify(data)
      });
      if (res.status === 403) throw new Error('Hanya Superadmin yang dapat membuat konten News.');
      if (!res.ok) throw new Error('Gagal menyimpan campaign');
      return res.json();
    },
    onSuccess: () => {
      toast.success('Newsletter dibuat!');
      setShowForm(false);
      setSubject(''); setContent('');
      queryClient.invalidateQueries({ queryKey: ['admin-campaigns'] });
    },
    onError: (err: any) => toast.error(err.message)
  });

  const blastCampaign = useMutation({
    mutationFn: async (id: number) => {
      const res = await fetch(`/admin/api/newsletter/campaigns/${id}/blast`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '' }
      });
      if (!res.ok) {
         const data = await res.json().catch(()=>({}));
         throw new Error(data.message || 'Gagal blast email');
      }
      return res.json();
    },
    onSuccess: (data) => {
      toast.success(data.message || 'Email berhasil dikirim!');
      queryClient.invalidateQueries({ queryKey: ['admin-campaigns'] });
    },
    onError: (err: any) => toast.error(err.message)
  });

  const delSub = async (id: number) => {
     if (!confirm('Yakin hapus?')) return;
     const res = await fetch(`/admin/api/newsletter/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '' }
     });
     if (res.ok) { toast.success('Dihapus'); queryClient.invalidateQueries({ queryKey: ['admin-newsletter'] }); }
  };

  // Tables Setup
  const subCols = [
    subHelper.accessor('email', { header: 'Email', cell: i => <span className="font-medium">{i.getValue()}</span> }),
    subHelper.accessor('created_at', { header: 'Tgl Subscribe', cell: i => new Date(i.getValue()).toLocaleDateString('id-ID') }),
    subHelper.display({ id: 'actions', header: 'Aksi', cell: p => <button className="text-red-600 hover:underline text-sm" onClick={() => delSub(p.row.original.id)}>Hapus</button> }),
  ];

  const campCols = [
    campHelper.accessor('subject', { header: 'Subjek', cell: i => <span className="font-semibold">{i.getValue()}</span> }),
    campHelper.accessor('creator.name', { header: 'Pembuat', cell: i => i.getValue() || '-' }),
    campHelper.accessor('status', { header: 'Status', cell: i => (
      <span className={`px-2 py-1 rounded text-xs ${i.getValue() === 'sent' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}`}>
        {i.getValue() === 'sent' ? 'Terkirim' : 'Draft'}
      </span>
    )}),
    campHelper.accessor('sent_at', { header: 'Tgl Kirim', cell: i => i.getValue() ? new Date(i.getValue()).toLocaleString('id-ID') : '-' }),
    campHelper.display({ id: 'actions', header: 'Aksi', cell: p => (
      p.row.original.status === 'draft' ? (
        <button 
          className="bg-black text-white px-3 py-1 rounded text-sm hover:bg-gray-800"
          onClick={() => {
            if(confirm(`Blast campaign "${p.row.original.subject}" ke SEMUA subscriber?`)) {
              toast.info('Mengirim email... Mohon tunggu.');
              blastCampaign.mutate(p.row.original.id);
            }
          }}
          disabled={blastCampaign.isPending}
        >
          {blastCampaign.isPending ? 'Mengirim...' : 'Blast Email'}
        </button>
      ) : <span className="text-gray-400 text-sm italic">Terkirim</span>
    ) }),
  ];

  const subTable = useReactTable({ data: subData?.data ?? [], columns: subCols, getCoreRowModel: getCoreRowModel() });
  const campTable = useReactTable({ data: campData ?? [], columns: campCols, getCoreRowModel: getCoreRowModel() });

  return (
    <div className="bg-white p-6 rounded-xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] space-y-6">
      
      {/* Tabs */}
      <div className="flex border-b">
        <button 
          className={`py-2 px-4 font-medium text-sm ${activeTab === 'subscribers' ? 'border-b-2 border-black text-black' : 'text-gray-500'}`}
          onClick={() => setActiveTab('subscribers')}
        >
          Subscribers
        </button>
        <button 
          className={`py-2 px-4 font-medium text-sm ${activeTab === 'campaigns' ? 'border-b-2 border-black text-black' : 'text-gray-500'}`}
          onClick={() => setActiveTab('campaigns')}
        >
          Email Campaigns
        </button>
      </div>

      {activeTab === 'subscribers' && (
        <div className="space-y-4">
          <input 
            type="text" placeholder="Cari email..." 
            className="border rounded-md p-2 w-64" value={search} onChange={e => setSearch(e.target.value)}
          />
          {subLoading ? <p>Loading...</p> : (
            <table className="w-full text-left">
              <thead>
                {subTable.getHeaderGroups().map(hg => (
                  <tr key={hg.id} className="border-b bg-gray-50/50"><th colSpan={3} className="hidden"></th>
                    {hg.headers.map(h => <th key={h.id} className="p-3 text-xs text-gray-500 uppercase">{flexRender(h.column.columnDef.header, h.getContext())}</th>)}
                  </tr>
                ))}
              </thead>
              <tbody>
                {subTable.getRowModel().rows.map(r => (
                  <tr key={r.id} className="border-b hover:bg-gray-50/50">
                    {r.getVisibleCells().map(c => <td key={c.id} className="p-3 text-sm">{flexRender(c.column.columnDef.cell, c.getContext())}</td>)}
                  </tr>
                ))}
              </tbody>
            </table>
          )}
          <div className="flex justify-between items-center text-sm text-gray-500">
             <span>Hal {page} / {subData?.last_page || 1}</span>
             <div className="space-x-2">
               <button className="border px-3 py-1 rounded" disabled={page === 1} onClick={() => setPage(p => p-1)}>Prev</button>
               <button className="border px-3 py-1 rounded" disabled={page >= (subData?.last_page || 1)} onClick={() => setPage(p => p+1)}>Next</button>
             </div>
          </div>
        </div>
      )}

      {activeTab === 'campaigns' && (
        <div className="space-y-4">
          <div className="flex justify-between items-center">
            <h3 className="font-semibold text-gray-800">Daftar Newsletter Campaign</h3>
            <button onClick={() => setShowForm(!showForm)} className="bg-black text-white px-4 py-2 rounded text-sm">
              {showForm ? 'Batal' : '+ Buat News (Superadmin)'}
            </button>
          </div>

          {showForm && (
            <div className="bg-gray-50 p-4 rounded-md border border-gray-200 mb-6 space-y-4">
              <div>
                <label className="block text-sm font-medium mb-1">Subjek Email</label>
                <input type="text" value={subject} onChange={e => setSubject(e.target.value)} className="w-full border rounded p-2" placeholder="Promo Akhir Tahun..." />
              </div>
              <div>
                <label className="block text-sm font-medium mb-1">Isi Konten (Teks)</label>
                <textarea value={content} onChange={e => setContent(e.target.value)} className="w-full border rounded p-2 h-32" placeholder="Halo pelanggan setia..."></textarea>
              </div>
              <button 
                onClick={() => createCampaign.mutate({ subject, content })}
                disabled={!subject || !content || createCampaign.isPending}
                className="bg-indigo-600 text-white px-4 py-2 rounded text-sm disabled:opacity-50"
              >
                {createCampaign.isPending ? 'Menyimpan...' : 'Simpan Sebagai Draft'}
              </button>
              <p className="text-xs text-gray-500 mt-2">Hanya Superadmin yang diizinkan untuk membuat template konten. Admin biasa hanya bisa menekan tombol Blast.</p>
            </div>
          )}

          {campLoading ? <p>Loading...</p> : (
            <table className="w-full text-left">
              <thead>
                {campTable.getHeaderGroups().map(hg => (
                  <tr key={hg.id} className="border-b bg-gray-50/50">
                    {hg.headers.map(h => <th key={h.id} className="p-3 text-xs text-gray-500 uppercase">{flexRender(h.column.columnDef.header, h.getContext())}</th>)}
                  </tr>
                ))}
              </thead>
              <tbody>
                {campTable.getRowModel().rows.length === 0 ? <tr><td colSpan={5} className="p-4 text-center text-gray-500">Belum ada campaign.</td></tr> :
                  campTable.getRowModel().rows.map(r => (
                  <tr key={r.id} className="border-b hover:bg-gray-50/50">
                    {r.getVisibleCells().map(c => <td key={c.id} className="p-3 text-sm">{flexRender(c.column.columnDef.cell, c.getContext())}</td>)}
                  </tr>
                ))}
              </tbody>
            </table>
          )}
        </div>
      )}

    </div>
  );
}
