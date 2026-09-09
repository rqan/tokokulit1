@extends('admin.layout.main')
@section('title', 'Analisis Penjualan')
@section('content')

<div class="mb-6">
    <h2 class="display-font text-2xl">Analisis Penjualan</h2>
    <p class="text-sm text-lightMuted dark:text-darkMuted">Ringkasan performa penjualan dan pesanan toko.</p>
</div>

<!-- React Mount Point -->
<div id="react-sales-dashboard"></div>

@viteReactRefresh
@vite('resources/js/admin/app.tsx')

@endsection
