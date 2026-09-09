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
    subHelper.accessor('email', { header: 'Email', cell: i => <span className="font-semibold">{i.getValue()}</span> }),
    subHelper.accessor('created_at', { header: 'Tgl Subscribe', cell: i => new Date(i.getValue()).toLocaleDateString('id-ID') }),
    subHelper.display({ id: 'actions', header: 'Aksi', cell: p => <button className="text-red-500 hover:underline text-[10px] font-bold uppercase tracking-widest" onClick={() => delSub(p.row.original.id)}>Hapus</button> }),
  ];

  const campCols = [
    campHelper.accessor('subject', { header: 'Subjek', cell: i => <span className="font-semibold">{i.getValue()}</span> }),
    campHelper.accessor('creator.name', { header: 'Pembuat', cell: i => i.getValue() || '-' }),
    campHelper.accessor('status', { header: 'Status', cell: i => (
      <span className={`px-2 py-1 rounded text-[10px] font-bold uppercase tracking-widest ${i.getValue() === 'sent' ? 'bg-green-500/10 text-green-500' : 'bg-yellow-500/10 text-yellow-500'}`}>
        {i.getValue() === 'sent' ? 'Terkirim' : 'Draft'}
      </span>
    )}),
    campHelper.accessor('sent_at', { header: 'Tgl Kirim', cell: i => i.getValue() ? new Date(i.getValue()).toLocaleString('id-ID') : '-' }),
    campHelper.display({ id: 'actions', header: 'Aksi', cell: p => (
      p.row.original.status === 'draft' ? (
        <button 
          className="bg-lightMain dark:bg-darkMain text-lightBg dark:text-darkBg px-3 py-1.5 rounded text-[10px] font-bold uppercase tracking-widest hover:opacity-90 transition-opacity"
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
      ) : <span className="text-lightMuted dark:text-darkMuted text-[10px] font-bold uppercase tracking-widest italic">Terkirim</span>
    ) }),
  ];

  const subTable = useReactTable({ data: subData?.data ?? [], columns: subCols, getCoreRowModel: getCoreRowModel() });
  const campTable = useReactTable({ data: campData ?? [], columns: campCols, getCoreRowModel: getCoreRowModel() });

  return (
    <div className="bg-lightBg dark:bg-darkBg p-6 rounded-xl border border-lightBorder dark:border-darkBorder shadow-sm space-y-6 text-lightMain dark:text-darkMain transition-colors">
      
      {/* Tabs */}
      <div className="flex border-b border-lightBorder dark:border-darkBorder">
        <button 
          className={`py-3 px-6 font-bold text-xs uppercase tracking-widest transition-colors ${activeTab === 'subscribers' ? 'border-b-2 border-lightMain dark:border-darkMain text-lightMain dark:text-darkMain' : 'text-lightMuted dark:text-darkMuted hover:text-lightMain dark:hover:text-darkMain'}`}
          onClick={() => setActiveTab('subscribers')}
        >
          Subscribers
        </button>
        <button 
          className={`py-3 px-6 font-bold text-xs uppercase tracking-widest transition-colors ${activeTab === 'campaigns' ? 'border-b-2 border-lightMain dark:border-darkMain text-lightMain dark:text-darkMain' : 'text-lightMuted dark:text-darkMuted hover:text-lightMain dark:hover:text-darkMain'}`}
          onClick={() => setActiveTab('campaigns')}
        >
          Email Campaigns
        </button>
      </div>

      {activeTab === 'subscribers' && (
        <div className="space-y-4">
          <input 
            type="text" placeholder="Cari email..." 
            className="border border-lightBorder dark:border-darkBorder rounded-md p-3 w-64 bg-transparent text-sm focus:outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors" 
            value={search} onChange={e => setSearch(e.target.value)}
          />
          {subLoading ? <p className="text-sm text-lightMuted dark:text-darkMuted">Loading...</p> : (
            <div className="overflow-x-auto">
              <table className="w-full text-left border-collapse">
                <thead>
                  {subTable.getHeaderGroups().map(hg => (
                    <tr key={hg.id} className="border-b-minimal border-lightBorder dark:border-darkBorder">
                      {hg.headers.map(h => <th key={h.id} className="py-3 px-4 text-[10px] font-bold text-lightMuted dark:text-darkMuted uppercase tracking-widest">{flexRender(h.column.columnDef.header, h.getContext())}</th>)}
                    </tr>
                  ))}
                </thead>
                <tbody>
                  {subTable.getRowModel().rows.map(r => (
                    <tr key={r.id} className="border-b-minimal border-lightBorder dark:border-darkBorder hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                      {r.getVisibleCells().map(c => <td key={c.id} className="py-4 px-4 text-sm">{flexRender(c.column.columnDef.cell, c.getContext())}</td>)}
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
          <div className="flex justify-between items-center text-xs font-bold tracking-widest uppercase text-lightMuted dark:text-darkMuted">
             <span>Hal {page} / {subData?.last_page || 1}</span>
             <div className="space-x-2">
               <button className="border border-lightBorder dark:border-darkBorder px-4 py-2 rounded hover:bg-black/5 dark:hover:bg-white/5 disabled:opacity-50 transition-colors" disabled={page === 1} onClick={() => setPage(p => p-1)}>Prev</button>
               <button className="border border-lightBorder dark:border-darkBorder px-4 py-2 rounded hover:bg-black/5 dark:hover:bg-white/5 disabled:opacity-50 transition-colors" disabled={page >= (subData?.last_page || 1)} onClick={() => setPage(p => p+1)}>Next</button>
             </div>
          </div>
        </div>
      )}

      {activeTab === 'campaigns' && (
        <div className="space-y-6">
          <div className="flex justify-between items-center">
            <h3 className="font-bold text-xs uppercase tracking-[0.2em] text-lightMain dark:text-darkMain">Daftar Newsletter Campaign</h3>
            <button onClick={() => setShowForm(!showForm)} className="bg-lightMain dark:bg-darkMain text-lightBg dark:text-darkBg px-4 py-2 rounded text-[10px] font-bold tracking-widest uppercase hover:opacity-90 transition-opacity">
              {showForm ? 'Batal' : '+ Buat News (Superadmin)'}
            </button>
          </div>

          {showForm && (
            <div className="bg-black/5 dark:bg-white/5 p-6 rounded-lg border border-lightBorder dark:border-darkBorder space-y-4">
              <div>
                <label className="block text-[10px] font-bold tracking-widest uppercase text-lightMuted dark:text-darkMuted mb-2">Subjek Email</label>
                <input type="text" value={subject} onChange={e => setSubject(e.target.value)} className="w-full bg-transparent border border-lightBorder dark:border-darkBorder rounded p-3 text-sm outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors" placeholder="Promo Akhir Tahun..." />
              </div>
              <div>
                <label className="block text-[10px] font-bold tracking-widest uppercase text-lightMuted dark:text-darkMuted mb-2">Isi Konten (Teks)</label>
                <textarea value={content} onChange={e => setContent(e.target.value)} className="w-full bg-transparent border border-lightBorder dark:border-darkBorder rounded p-3 text-sm h-32 outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors" placeholder="Halo pelanggan setia..."></textarea>
              </div>
              <button 
                onClick={() => createCampaign.mutate({ subject, content })}
                disabled={!subject || !content || createCampaign.isPending}
                className="bg-lightMain dark:bg-darkMain text-lightBg dark:text-darkBg px-6 py-3 rounded text-[10px] font-bold uppercase tracking-widest disabled:opacity-50 hover:opacity-90 transition-opacity"
              >
                {createCampaign.isPending ? 'Menyimpan...' : 'Simpan Sebagai Draft'}
              </button>
              <p className="text-[10px] text-lightMuted dark:text-darkMuted mt-2">*Hanya Superadmin yang diizinkan untuk membuat template konten. Admin biasa hanya bisa menekan tombol Blast.</p>
            </div>
          )}

          {campLoading ? <p className="text-sm text-lightMuted dark:text-darkMuted">Loading...</p> : (
            <div className="overflow-x-auto">
              <table className="w-full text-left border-collapse">
                <thead>
                  {campTable.getHeaderGroups().map(hg => (
                    <tr key={hg.id} className="border-b-minimal border-lightBorder dark:border-darkBorder">
                      {hg.headers.map(h => <th key={h.id} className="py-3 px-4 text-[10px] font-bold text-lightMuted dark:text-darkMuted uppercase tracking-widest">{flexRender(h.column.columnDef.header, h.getContext())}</th>)}
                    </tr>
                  ))}
                </thead>
                <tbody>
                  {campTable.getRowModel().rows.length === 0 ? <tr><td colSpan={5} className="py-8 px-4 text-center text-sm text-lightMuted dark:text-darkMuted">Belum ada campaign.</td></tr> :
                    campTable.getRowModel().rows.map(r => (
                    <tr key={r.id} className="border-b-minimal border-lightBorder dark:border-darkBorder hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                      {r.getVisibleCells().map(c => <td key={c.id} className="py-4 px-4 text-sm">{flexRender(c.column.columnDef.cell, c.getContext())}</td>)}
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>
      )}

    </div>
  );
}
