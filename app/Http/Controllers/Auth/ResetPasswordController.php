<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, string $token)
    {
        $email = $request->query('email');
        return view('auth.reset-password', compact('token', 'email'));
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->with('error', 'Token reset password tidak valid atau telah kedaluwarsa.');
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->update(['password' => $request->password]); // User model auto-hashes via 'hashed' cast
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return redirect('/login')->with('success', 'Password Anda berhasil diperbarui. Silakan login.');
        }

        return back()->with('error', 'Pengguna tidak ditemukan.');
    }
}
