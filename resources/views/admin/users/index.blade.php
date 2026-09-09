@extends('admin.layout.main')
@section('title', $title ?? 'Users')
@section('content')

<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="display-font text-2xl">Manajemen Pengguna</h2>
    </div>
</div>

<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b-minimal border-lightBorder dark:border-darkBorder">
                <th class="py-3 px-4 text-xs font-semibold uppercase tracking-widest">ID</th>
                <th class="py-3 px-4 text-xs font-semibold uppercase tracking-widest">Username</th>
                <th class="py-3 px-4 text-xs font-semibold uppercase tracking-widest">Email</th>
                <th class="py-3 px-4 text-xs font-semibold uppercase tracking-widest">Role</th>
                <th class="py-3 px-4 text-xs font-semibold uppercase tracking-widest text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr class="border-b-minimal border-lightBorder dark:border-darkBorder hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                    <td class="py-4 px-4 text-sm">{{ $user->id ?? '' }}</td>
                    <td class="py-4 px-4 text-sm font-semibold">{{ $user->name ?? '' }}</td>
                    <td class="py-4 px-4 text-sm">{{ $user->email ?? '' }}</td>
                    <td class="py-4 px-4 text-sm">
                        <form action="{{ url('/admin/users/change-role/' . ($user->id ?? '')) }}" method="post" class="inline">
                            @csrf
                            <select name="role" class="bg-transparent dark:bg-darkBg border-minimal border-lightBorder text-xs p-1 rounded focus:outline-none" onchange="this.form.submit()">
                                <option value="pelanggan" class="bg-lightBg dark:bg-darkBg text-lightMain dark:text-darkMain" {{ ($user->role ?? '') == 'pelanggan' ? 'selected' : '' }}>Pelanggan</option>
                                <option value="admin" class="bg-lightBg dark:bg-darkBg text-lightMain dark:text-darkMain" {{ ($user->role ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="superadmin" class="bg-lightBg dark:bg-darkBg text-lightMain dark:text-darkMain" {{ ($user->role ?? '') == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                            </select>
                        </form>
                    </td>
                    <td class="py-4 px-4 text-sm flex justify-end gap-3">
                        <a href="{{ url('/admin/users/delete/' . ($user->id ?? '')) }}" onclick="return confirm('Yakin ingin menghapus?')" class="text-red-500 hover:underline uppercase text-[10px] font-semibold tracking-widest">Hapus</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-8 text-center text-sm">Belum ada user.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
