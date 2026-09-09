<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revamp Panel Admin - Toko Kulit 1</title>
    
    <!-- Vite Directives for React and CSS -->
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/admin/app.tsx'])
    
    <!-- Optional: Tailwind config if needed globally, but Vite should handle it via React app -->
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden; /* React handles its own scrolling inside layout */
        }
    </style>
</head>
<body>
    <div id="react-admin-revamp"></div>
</body>
</html>
