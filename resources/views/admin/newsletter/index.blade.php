@extends('admin.layout.main')
@section('title', 'Manajemen Newsletter')
@section('content')

<div class="mb-6">
    <h2 class="display-font text-2xl">Newsletter Subscribers</h2>
    <p class="text-sm text-lightMuted dark:text-darkMuted">Kelola langganan email dan blast promosi.</p>
</div>

<!-- React Mount Point -->
<div id="react-newsletter-table"></div>

@viteReactRefresh
@vite('resources/js/admin/app.tsx')

@endsection
