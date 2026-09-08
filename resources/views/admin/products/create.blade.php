@extends('admin.layout.main')
@section('title', 'Tambah Produk Baru')
@section('content')

<div class="mb-6">
    <h2 class="display-font text-2xl">Tambah Produk</h2>
    <p class="text-sm text-lightMuted dark:text-darkMuted">Formulir dinamis berbasis React Hook Form & Zod.</p>
</div>

<!-- React Mount Point -->
<div id="react-product-form"></div>

@viteReactRefresh
@vite('resources/js/admin/app.tsx')

@endsection
