@extends('admin.layout.main')
@section('title', $title ?? 'Daftar Produk')
@section('content')

<div class="mb-6">
    <h2 class="display-font text-2xl">Daftar Produk</h2>
    <p class="text-sm text-lightMuted dark:text-darkMuted">Manajemen produk pada katalog.</p>
</div>

<!-- React Mount Point -->
<div id="react-product-table"></div>

@viteReactRefresh
@vite('resources/js/admin/app.tsx')

@endsection
