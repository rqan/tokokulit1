import React, { useState } from 'react';
import { 
  LayoutDashboard, 
  ShoppingCart, 
  Users, 
  Package, 
  Settings, 
  Search, 
  Bell, 
  Menu, 
  Filter, 
  MoreVertical,
  ChevronDown,
  Download,
  Plus
} from 'lucide-react';
import { 
  LineChart, 
  Line, 
  XAxis, 
  YAxis, 
  CartesianGrid, 
  Tooltip, 
  ResponsiveContainer 
} from 'recharts';

// --- MOCK DATA ---
const chartData = [
  { name: 'Jan', revenue: 4000, orders: 240 },
  { name: 'Feb', revenue: 3000, orders: 139 },
  { name: 'Mar', revenue: 2000, orders: 980 },
  { name: 'Apr', revenue: 2780, orders: 390 },
  { name: 'May', revenue: 1890, orders: 480 },
  { name: 'Jun', revenue: 2390, orders: 380 },
  { name: 'Jul', revenue: 3490, orders: 430 },
];

const mockTableData = Array.from({ length: 5 }).map((_, i) => ({
  id: `ORD-2024-${1000 + i}`,
  customer: `Pelanggan ${i + 1}`,
  email: `email${i}@example.com`,
  phone: `0812345678${i}`,
  status: i % 2 === 0 ? 'Completed' : 'Pending',
  date: '2024-05-10',
  total: `Rp ${(Math.random() * 1000000).toFixed(0)}`,
  payment: 'Transfer Bank',
  courier: 'JNE',
  receipt: `JNE${Math.floor(Math.random() * 1000000)}`,
  address: 'Jl. Sudirman No.1, Jakarta',
  notes: 'Kirim pagi',
  discount: 'Rp 0',
  tax: 'Rp 10.000',
  items: `${Math.floor(Math.random() * 5) + 1} item(s)`,
}));

export default function AdminRevamp() {
  const [activeTab, setActiveTab] = useState('dashboard');
  const [sidebarOpen, setSidebarOpen] = useState(true);
  const [filterOpen, setFilterOpen] = useState(false);

  return (
    <div className="flex h-screen bg-slate-50 font-sans text-slate-800">
      {/* SIDEBAR */}
      <aside 
        className={`${sidebarOpen ? 'w-64' : 'w-20'} transition-all duration-300 bg-white border-r border-slate-200 flex flex-col`}
      >
        <div className="h-16 flex items-center justify-between px-4 border-b border-slate-200">
          {sidebarOpen && <span className="font-bold text-xl text-indigo-600">AdminPanel</span>}
          <button onClick={() => setSidebarOpen(!sidebarOpen)} className="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500">
            <Menu size={20} />
          </button>
        </div>
        
        <nav className="flex-1 p-4 space-y-2 overflow-y-auto">
          {[
            { id: 'dashboard', icon: LayoutDashboard, label: 'Dashboard' },
            { id: 'orders', icon: ShoppingCart, label: 'Pesanan (15 Kolom)' },
            { id: 'form', icon: Package, label: 'Produk Baru (Form)' },
            { id: 'customers', icon: Users, label: 'Pelanggan' },
            { id: 'settings', icon: Settings, label: 'Pengaturan' }
          ].map(item => (
            <button
              key={item.id}
              onClick={() => setActiveTab(item.id)}
              className={`w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors ${
                activeTab === item.id ? 'bg-indigo-50 text-indigo-600 font-medium' : 'text-slate-600 hover:bg-slate-100'
              }`}
            >
              <item.icon size={20} className={activeTab === item.id ? 'text-indigo-600' : 'text-slate-400'} />
              {sidebarOpen && <span>{item.label}</span>}
            </button>
          ))}
        </nav>
      </aside>

      {/* MAIN LAYOUT */}
      <main className="flex-1 flex flex-col h-screen overflow-hidden">
        {/* TOPBAR */}
        <header className="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 shrink-0">
          <div className="flex items-center gap-4 bg-slate-100 px-3 py-2 rounded-lg w-96">
            <Search size={18} className="text-slate-400" />
            <input 
              type="text" 
              placeholder="Cari pesanan, produk, atau pelanggan..." 
              className="bg-transparent border-none outline-none w-full text-sm placeholder:text-slate-400"
            />
          </div>
          <div className="flex items-center gap-4">
            <button className="relative p-2 text-slate-400 hover:bg-slate-100 rounded-full">
              <Bell size={20} />
              <span className="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>
            <div className="w-9 h-9 rounded-full bg-indigo-100 border border-indigo-200 flex items-center justify-center text-indigo-700 font-bold">
              AD
            </div>
          </div>
        </header>

        {/* CONTENT AREA */}
        <div className="flex-1 overflow-y-auto p-6">
          
          {/* VIEW: DASHBOARD */}
          {activeTab === 'dashboard' && (
            <div className="space-y-6">
              <h1 className="text-2xl font-bold text-slate-800">Ikhtisar Performa</h1>
              
              {/* KPI Cards */}
              <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                {[
                  { title: 'Total Pendapatan', value: 'Rp 124.500.000', trend: '+12.5%' },
                  { title: 'Pesanan Baru', value: '342', trend: '+5.2%' },
                  { title: 'Pesanan Menunggu Dikirim', value: '28', trend: '-2.1%', isAlert: true },
                ].map((kpi, i) => (
                  <div key={i} className="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                    <p className="text-sm text-slate-500 font-medium mb-1">{kpi.title}</p>
                    <div className="flex items-end gap-3">
                      <h2 className="text-2xl font-bold text-slate-800">{kpi.value}</h2>
                      <span className={`text-sm font-medium ${kpi.isAlert ? 'text-red-500' : 'text-emerald-500'}`}>
                        {kpi.trend}
                      </span>
                    </div>
                  </div>
                ))}
              </div>

              {/* Chart */}
              <div className="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h3 className="font-semibold text-slate-800 mb-6">Tren Penjualan (7 Bulan Terakhir)</h3>
                <div className="h-72">
                  <ResponsiveContainer width="100%" height="100%">
                    <LineChart data={chartData}>
                      <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#e2e8f0" />
                      <XAxis dataKey="name" axisLine={false} tickLine={false} tick={{fill: '#64748b'}} />
                      <YAxis axisLine={false} tickLine={false} tick={{fill: '#64748b'}} />
                      <Tooltip 
                        contentStyle={{ borderRadius: '8px', border: 'none', boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1)' }}
                      />
                      <Line type="monotone" dataKey="revenue" stroke="#4f46e5" strokeWidth={3} dot={{r: 4}} activeDot={{r: 6}} />
                    </LineChart>
                  </ResponsiveContainer>
                </div>
              </div>
            </div>
          )}

          {/* VIEW: TABLE (15 KOLOM) */}
          {activeTab === 'orders' && (
            <div className="bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col h-full">
              {/* Table Header / Toolbar */}
              <div className="p-4 border-b border-slate-200 flex items-center justify-between flex-wrap gap-4">
                <h2 className="text-lg font-bold text-slate-800">Data Pesanan</h2>
                <div className="flex gap-2">
                  <button 
                    onClick={() => setFilterOpen(!filterOpen)}
                    className={`flex items-center gap-2 px-3 py-2 border rounded-lg text-sm font-medium transition-colors ${filterOpen ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : 'border-slate-200 hover:bg-slate-50 text-slate-600'}`}
                  >
                    <Filter size={16} /> Filter Kompleks
                  </button>
                  <button className="flex items-center gap-2 px-3 py-2 border border-slate-200 hover:bg-slate-50 rounded-lg text-sm font-medium text-slate-600">
                    <Download size={16} /> Ekspor
                  </button>
                </div>
              </div>

              {/* Accordion Filter Area */}
              {filterOpen && (
                <div className="p-4 bg-slate-50 border-b border-slate-200 grid grid-cols-4 gap-4">
                  <div>
                    <label className="block text-xs font-medium text-slate-500 mb-1">Status Pesanan</label>
                    <select className="w-full text-sm border-slate-300 rounded-md shadow-sm"><option>Semua Status</option><option>Pending</option></select>
                  </div>
                  <div>
                    <label className="block text-xs font-medium text-slate-500 mb-1">Rentang Tanggal</label>
                    <input type="date" className="w-full text-sm border-slate-300 rounded-md shadow-sm" />
                  </div>
                  <div>
                    <label className="block text-xs font-medium text-slate-500 mb-1">Metode Pembayaran</label>
                    <select className="w-full text-sm border-slate-300 rounded-md shadow-sm"><option>Semua</option><option>Transfer</option></select>
                  </div>
                  <div className="flex items-end">
                    <button className="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-md text-sm font-medium">Terapkan Filter</button>
                  </div>
                </div>
              )}

              {/* Table Container (Horizontal Scroll with Sticky Columns) */}
              <div className="overflow-x-auto flex-1 relative">
                <table className="w-full text-left text-sm whitespace-nowrap">
                  <thead className="bg-slate-50 text-slate-500 border-b border-slate-200 sticky top-0 z-10">
                    <tr>
                      <th className="px-4 py-3 font-medium sticky left-0 bg-slate-50 shadow-[1px_0_0_0_#e2e8f0] z-20">ID Pesanan</th>
                      <th className="px-4 py-3 font-medium">Pelanggan</th>
                      <th className="px-4 py-3 font-medium">Status</th>
                      <th className="px-4 py-3 font-medium">Total</th>
                      <th className="px-4 py-3 font-medium">Tanggal</th>
                      <th className="px-4 py-3 font-medium">Email</th>
                      <th className="px-4 py-3 font-medium">No. HP</th>
                      <th className="px-4 py-3 font-medium">Pembayaran</th>
                      <th className="px-4 py-3 font-medium">Kurir</th>
                      <th className="px-4 py-3 font-medium">No. Resi</th>
                      <th className="px-4 py-3 font-medium">Jumlah Item</th>
                      <th className="px-4 py-3 font-medium">Pajak</th>
                      <th className="px-4 py-3 font-medium">Diskon</th>
                      <th className="px-4 py-3 font-medium">Catatan</th>
                      <th className="px-4 py-3 font-medium sticky right-0 bg-slate-50 shadow-[-1px_0_0_0_#e2e8f0] z-20 text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-200">
                    {mockTableData.map((row) => (
                      <tr key={row.id} className="hover:bg-slate-50 transition-colors group">
                        <td className="px-4 py-3 font-medium text-slate-900 sticky left-0 bg-white group-hover:bg-slate-50 shadow-[1px_0_0_0_#e2e8f0]">{row.id}</td>
                        <td className="px-4 py-3 text-slate-700">{row.customer}</td>
                        <td className="px-4 py-3">
                          <span className={`px-2 py-1 rounded-full text-xs font-medium ${row.status === 'Completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'}`}>
                            {row.status}
                          </span>
                        </td>
                        <td className="px-4 py-3 text-slate-900 font-medium">{row.total}</td>
                        <td className="px-4 py-3 text-slate-600">{row.date}</td>
                        <td className="px-4 py-3 text-slate-600">{row.email}</td>
                        <td className="px-4 py-3 text-slate-600">{row.phone}</td>
                        <td className="px-4 py-3 text-slate-600">{row.payment}</td>
                        <td className="px-4 py-3 text-slate-600">{row.courier}</td>
                        <td className="px-4 py-3 text-slate-600">{row.receipt}</td>
                        <td className="px-4 py-3 text-slate-600">{row.items}</td>
                        <td className="px-4 py-3 text-slate-600">{row.tax}</td>
                        <td className="px-4 py-3 text-slate-600">{row.discount}</td>
                        <td className="px-4 py-3 text-slate-600 truncate max-w-[150px]">{row.notes}</td>
                        <td className="px-4 py-3 sticky right-0 bg-white group-hover:bg-slate-50 shadow-[-1px_0_0_0_#e2e8f0] text-center">
                          <button className="p-1 hover:bg-slate-200 rounded text-slate-500">
                            <MoreVertical size={16} />
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
              <div className="p-4 border-t border-slate-200 text-sm text-slate-500 flex justify-between items-center">
                <span>Menampilkan 1-5 dari 142 data</span>
                <div className="flex gap-1">
                  <button className="px-3 py-1 border rounded hover:bg-slate-50">Prev</button>
                  <button className="px-3 py-1 bg-indigo-600 text-white rounded">1</button>
                  <button className="px-3 py-1 border rounded hover:bg-slate-50">2</button>
                  <button className="px-3 py-1 border rounded hover:bg-slate-50">Next</button>
                </div>
              </div>
            </div>
          )}

          {/* VIEW: COMPLEX FORM (Multi-step / Sections) */}
          {activeTab === 'form' && (
            <div className="max-w-4xl mx-auto space-y-6 pb-20">
              <div className="flex items-center justify-between">
                <div>
                  <h1 className="text-2xl font-bold text-slate-800">Tambah Produk Baru</h1>
                  <p className="text-slate-500 text-sm mt-1">Lengkapi informasi di bawah untuk menambahkan produk ke katalog.</p>
                </div>
                <div className="flex gap-3">
                  <button className="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 font-medium">Batal</button>
                  <button className="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">Simpan Produk</button>
                </div>
              </div>

              {/* Form Layout with Tabs/Cards instead of one long scroll */}
              <div className="grid grid-cols-3 gap-6">
                {/* Form Sections Navigation */}
                <div className="col-span-1">
                  <div className="bg-white rounded-xl border border-slate-200 p-2 sticky top-6">
                    {['Informasi Umum', 'Harga & Stok', 'Media (Gambar)', 'SEO & Meta'].map((section, idx) => (
                      <button key={idx} className={`w-full text-left px-4 py-2.5 rounded-lg text-sm font-medium mb-1 ${idx === 0 ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50'}`}>
                        {section}
                      </button>
                    ))}
                  </div>
                </div>

                {/* Form Fields Area */}
                <div className="col-span-2 space-y-6">
                  {/* Section 1 */}
                  <div className="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                    <h3 className="text-lg font-bold text-slate-800 mb-4 border-b pb-2">Informasi Umum</h3>
                    <div className="space-y-4">
                      <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">Nama Produk <span className="text-red-500">*</span></label>
                        <input type="text" className="w-full border-slate-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Misal: Tas Kulit Asli Pria" />
                      </div>
                      <div className="grid grid-cols-2 gap-4">
                        <div>
                          <label className="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                          <select className="w-full border-slate-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option>Pilih Kategori</option>
                            <option>Tas Pria</option>
                            <option>Dompet</option>
                          </select>
                        </div>
                        <div>
                          <label className="block text-sm font-medium text-slate-700 mb-1">Brand / Merk</label>
                          <input type="text" className="w-full border-slate-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">Deskripsi Lengkap</label>
                        <textarea rows={4} className="w-full border-slate-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Tuliskan spesifikasi produk..."></textarea>
                      </div>
                    </div>
                  </div>

                  {/* Section 2 */}
                  <div className="bg-white rounded-xl border border-slate-200 p-6 shadow-sm opacity-60 pointer-events-none">
                    <h3 className="text-lg font-bold text-slate-800 mb-4 border-b pb-2">Harga & Stok (Contoh Lanjutan)</h3>
                    {/* Dimmed to show it exists but focusing on layout strategy */}
                    <div className="h-20 bg-slate-100 rounded-lg border border-dashed border-slate-300 flex items-center justify-center text-slate-400">
                      Form Fields...
                    </div>
                  </div>
                </div>
              </div>
            </div>
          )}

        </div>
      </main>
    </div>
  );
}
