<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function attemptLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            
            // Set additional session logic for compatibility with the old CodeIgniter flow if needed
            $request->session()->put([
                'user_id' => $user->id,
                'username' => $user->username ?? 'user',
                'role' => $user->role ?? 'pelanggan',
                'isLoggedIn' => true,
            ]);

            if (in_array($user->role, ['admin', 'superadmin'])) {
                return redirect()->intended('/admin/dashboard');
            } else {
                return redirect()->intended('/');
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function attemptRegister(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|min:3|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $validated['username'],
            'email' => $validated['email'],
            'password' => $validated['password'], // User model auto-hashes via 'hashed' cast
            'role' => 'pelanggan'
        ]);

        return redirect('/login')->with('message', 'Registrasi berhasil, silakan login.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
