<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind-config.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ux.css') }}">
    <script src="{{ asset('js/ux.js') }}"></script>
    
    <style>
        .border-minimal { border-width: 1px; border-style: solid; }
    </style>
    <!-- Simple DataTables -->
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
    <style>
        /* Override Simple-DataTables styles for dark mode compatibility */
        .dataTable-wrapper { font-family: inherit; }
        .dataTable-top, .dataTable-bottom { padding: 1rem 0; }
        
        .dark .dataTable-wrapper input,
        .dark .dataTable-wrapper select,
        .dataTable-input, 
        .dataTable-selector { 
            background-color: transparent !important; 
            border: 1px solid #4a5568 !important; 
            color: inherit !important; 
            padding: 0.5rem; 
            border-radius: 0.25rem;
            outline: none;
        }
        
        .dark .dataTable-wrapper input:focus,
        .dark .dataTable-wrapper select:focus,
        .dataTable-input:focus { border-color: #fca311 !important; }
        
        .dark .dataTable-wrapper select option { background-color: #1a202c !important; color: #fff !important; }
        
        .dataTable-table > thead > tr > th { border-bottom: 1px solid #4a5568 !important; }
        .dataTable-table > tbody > tr > td { border-bottom: 1px solid #4a5568 !important; border-top: none !important; }
        
        .dataTable-pagination a {
            color: inherit !important;
            border: 1px solid transparent !important;
            border-radius: 0.25rem;
        }
        .dataTable-pagination a:hover {
            background-color: #fca311 !important;
            color: #1a202c !important;
        }
        .dataTable-pagination .active a {
            background-color: #fca311 !important;
            color: #1a202c !important;
        }
    </style>
</head>
<body class="bg-lightBg text-lightMain dark:bg-darkBg dark:text-darkMain min-h-screen flex">

    <!-- Sidebar -->
    @include('admin.layout.sidebar')

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-h-screen transition-colors duration-300 ml-64">
        <!-- Topbar -->
        @include('admin.layout.topbar')

        <!-- Content -->
        <main class="flex-1 p-8">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-400 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-400 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        const html = document.documentElement;
        const themeToggle = document.getElementById('themeToggle');
        
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }

        if(themeToggle) {
            themeToggle.addEventListener('click', () => {
                html.classList.toggle('dark');
                if (html.classList.contains('dark')) {
                    localStorage.theme = 'dark';
                } else {
                    localStorage.theme = 'light';
                }
            });
        }
    </script>
    <!-- Initialize DataTables -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tables = document.querySelectorAll("table");
            tables.forEach(function(table) {
                new simpleDatatables.DataTable(table, {
                    searchable: true,
                    sortable: true,
                    perPage: 15,
                    labels: {
                        placeholder: "Cari...",
                        perPage: "data per halaman",
                        noRows: "Tidak ada data ditemukan",
                        info: "Menampilkan {start} sampai {end} dari {rows} data"
                    }
                });
            });
        });
    </script>
</body>
</html>
