@extends('admin.layout.main')
@section('title', 'Manajemen Pesanan')
@section('content')

<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="display-font text-2xl">Manajemen Pesanan</h2>
        <p class="text-sm text-lightMuted dark:text-darkMuted">Kelola dan perbarui status pesanan pelanggan.</p>
    </div>
</div>

<!-- React Mount Point -->
<div id="react-order-table"></div>

@viteReactRefresh
@vite('resources/js/admin/app.tsx')

@endsection
