<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password — Eny Leather</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-white antialiased flex items-center justify-center min-h-screen p-4">
    <div class="max-w-md w-full bg-slate-800 border border-slate-700 p-8 rounded-xl space-y-6 shadow-xl">
        <div class="text-center space-y-2">
            <h1 class="text-2xl font-bold uppercase tracking-wider">Lupa Password</h1>
            <p class="text-xs text-slate-400">Masukkan email Anda untuk menerima link reset kata sandi.</p>
        </div>

        @if(session('success'))
            <div class="p-3 bg-emerald-500/10 border border-emerald-500 text-emerald-400 text-xs rounded break-all">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-3 bg-red-500/10 border border-red-500 text-red-400 text-xs rounded">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ url('/forgot-password') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs uppercase tracking-widest text-slate-300 font-bold mb-2">Alamat Email</label>
                <input type="email" name="email" required placeholder="nama@email.com" class="w-full bg-slate-900 border border-slate-700 p-3 rounded text-sm text-white focus:outline-none focus:border-amber-500">
            </div>
            <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-600 font-bold text-xs uppercase tracking-widest text-slate-950 rounded transition-colors">
                Kirim Link Reset Password
            </button>
        </form>

        <div class="text-center pt-2">
            <a href="{{ url('/login') }}" class="text-xs text-slate-400 hover:text-amber-400 transition-colors">← Kembali ke Login</a>
        </div>
    </div>
</body>
</html>
