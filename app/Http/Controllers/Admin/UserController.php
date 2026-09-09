<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = \App\Models\User::all();
        } catch (\Exception $e) {
            $users = collect([]);
        }

        $data = [
            'title' => 'Manajemen Pengguna - Panel Admin',
            'users' => $users
        ];
        return view('admin.users.index', $data);
    }

    public function changeRole(Request $request, $id)
    {
        $role = $request->input('role');
        try {
            DB::table('users')->where('id', $id)->update(['role' => $role]);
        } catch (\Exception $e) {}
        
        return redirect('/admin/users')->with('success', 'Role pengguna berhasil diubah.');
    }

    public function delete($id)
    {
        try {
            DB::table('users')->where('id', $id)->delete();
        } catch (\Exception $e) {}
        
        return redirect('/admin/users')->with('success', 'Pengguna berhasil dihapus.');
    }
}
