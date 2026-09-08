import React, { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import {
  AreaChart, Area, XAxis, YAxis, CartesianGrid, Tooltip as RechartsTooltip, ResponsiveContainer,
  PieChart, Pie, Cell, Legend
} from 'recharts';

const COLORS = ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'];

export default function SalesDashboard() {
  const [range, setRange] = useState('30d');

  const { data, isLoading, isError } = useQuery({
    queryKey: ['admin-sales-stats', range],
    queryFn: async () => {
      const res = await fetch(`/admin/api/sales/stats?range=${range}`);
      if (!res.ok) throw new Error('Network response was not ok');
      return res.json();
    }
  });

  const renderDelta = (delta: number, reverseColors = false) => {
    const isPositive = delta > 0;
    const isZero = delta === 0;
    
    let color = 'text-gray-500';
    let bgColor = 'bg-gray-100';
    if (!isZero) {
        if (reverseColors) {
            color = isPositive ? 'text-red-600' : 'text-green-600';
            bgColor = isPositive ? 'bg-red-50' : 'bg-green-50';
        } else {
            color = isPositive ? 'text-green-600' : 'text-red-600';
            bgColor = isPositive ? 'bg-green-50' : 'bg-red-50';
        }
    }

    return (
      <span className={`inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${color} ${bgColor}`}>
        {isPositive ? '↑' : isZero ? '—' : '↓'} {Math.abs(delta)}%
      </span>
    );
  };

  if (isLoading) {
    return <div className="animate-pulse space-y-4">
      <div className="flex justify-end mb-4"><div className="h-8 w-48 bg-gray-200 rounded"></div></div>
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4 h-28 bg-gray-200 rounded"></div>
      <div className="h-72 bg-gray-200 rounded"></div>
    </div>;
  }

  if (isError || !data) {
    return <div className="text-red-500 bg-red-50 p-4 rounded-md">Gagal memuat data analitik. Pastikan koneksi stabil.</div>;
  }

  return (
    <div className="space-y-6">
      {/* Filters */}
      <div className="flex justify-end space-x-2">
        {['7d', '30d', '90d'].map((r) => (
          <button
            key={r}
            onClick={() => setRange(r)}
            className={`px-4 py-1.5 text-sm font-medium rounded-md transition-colors ${
              range === r 
                ? 'bg-black text-white' 
                : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'
            }`}
          >
            {r === '7d' ? '7 Hari' : r === '30d' ? '30 Hari' : '90 Hari'}
          </button>
        ))}
      </div>

      {/* Metric Cards (Tremor/Stripe Style) */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {/* Total Revenue */}
        <div className="bg-white p-5 rounded-xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)]">
          <div className="flex justify-between items-start">
            <h3 className="text-sm font-medium text-gray-500">Total Penjualan</h3>
            {renderDelta(data.summary.revenue.delta)}
          </div>
          <p className="mt-4 text-3xl font-semibold tracking-tight text-gray-900 font-mono">
            Rp {(data.summary.revenue.value / 1000000).toFixed(1)}Jt
          </p>
        </div>

        {/* Total Orders */}
        <div className="bg-white p-5 rounded-xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)]">
          <div className="flex justify-between items-start">
            <h3 className="text-sm font-medium text-gray-500">Jumlah Pesanan</h3>
            {renderDelta(data.summary.orders.delta)}
          </div>
          <p className="mt-4 text-3xl font-semibold tracking-tight text-gray-900 font-mono">
            {data.summary.orders.value}
          </p>
        </div>

        {/* Average Order Value (AOV) */}
        <div className="bg-white p-5 rounded-xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)]">
          <div className="flex justify-between items-start">
            <h3 className="text-sm font-medium text-gray-500">Nilai Rata-rata</h3>
            {renderDelta(data.summary.aov.delta)}
          </div>
          <p className="mt-4 text-3xl font-semibold tracking-tight text-gray-900 font-mono">
            Rp {(data.summary.aov.value / 1000).toFixed(0)}k
          </p>
        </div>

        {/* Canceled Orders */}
        <div className="bg-white p-5 rounded-xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)]">
          <div className="flex justify-between items-start">
            <h3 className="text-sm font-medium text-gray-500">Order Dibatalkan</h3>
            {renderDelta(data.summary.canceled.delta, true)}
          </div>
          <p className="mt-4 text-3xl font-semibold tracking-tight text-gray-900 font-mono">
            {data.summary.canceled.value}
          </p>
        </div>
      </div>

      {/* Charts area */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {/* Main Trend Chart */}
        <div className="bg-white p-6 rounded-xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] lg:col-span-2">
          <h3 className="text-sm font-semibold text-gray-800 mb-6">Tren Pendapatan</h3>
          <div className="h-80">
            <ResponsiveContainer width="100%" height="100%">
              <AreaChart data={data.revenue_trend} margin={{ top: 0, right: 0, bottom: 0, left: -20 }}>
                <defs>
                  <linearGradient id="colorRevenue" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="5%" stopColor="#000" stopOpacity={0.1}/>
                    <stop offset="95%" stopColor="#000" stopOpacity={0}/>
                  </linearGradient>
                </defs>
                <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f3f4f6" />
                <XAxis dataKey="name" axisLine={false} tickLine={false} tick={{ fontSize: 12, fill: '#6b7280' }} dy={10} />
                <YAxis axisLine={false} tickLine={false} tick={{ fontSize: 12, fill: '#6b7280' }} tickFormatter={(val) => `Rp${val/1000000}M`} />
                <RechartsTooltip 
                  contentStyle={{ borderRadius: '8px', border: 'none', boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1)' }}
                  formatter={(value: number) => [`Rp ${value.toLocaleString('id-ID')}`, 'Pendapatan']} 
                  labelStyle={{ fontWeight: 'bold', color: '#374151', marginBottom: '4px' }}
                />
                <Area type="monotone" dataKey="revenue" stroke="#000" strokeWidth={2} fillOpacity={1} fill="url(#colorRevenue)" activeDot={{ r: 6, fill: '#000', stroke: '#fff', strokeWidth: 2 }} />
              </AreaChart>
            </ResponsiveContainer>
          </div>
        </div>

        {/* Breakdown Chart */}
        <div className="bg-white p-6 rounded-xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)]">
          <h3 className="text-sm font-semibold text-gray-800 mb-6">Status Pesanan</h3>
          <div className="h-80">
            {data.order_statuses.length > 0 ? (
                <ResponsiveContainer width="100%" height="100%">
                <PieChart>
                    <Pie
                      data={data.order_statuses}
                      cx="50%"
                      cy="45%"
                      innerRadius={65}
                      outerRadius={90}
                      paddingAngle={2}
                      dataKey="value"
                      stroke="none"
                    >
                    {data.order_statuses.map((entry: any, index: number) => (
                        <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                    ))}
                    </Pie>
                    <RechartsTooltip 
                       contentStyle={{ borderRadius: '8px', border: 'none', boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1)' }}
                       itemStyle={{ color: '#111827', fontWeight: 500 }}
                    />
                    <Legend verticalAlign="bottom" height={36} iconType="circle" wrapperStyle={{ fontSize: '12px' }}/>
                </PieChart>
                </ResponsiveContainer>
            ) : (
                <div className="h-full flex items-center justify-center text-gray-400 text-sm">
                    Belum ada data pesanan
                </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
