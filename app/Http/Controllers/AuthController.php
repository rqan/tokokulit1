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
        $request->validate([
            'login' => 'required',
            'password' => 'required'
        ]);

        $login = trim($request->input('login'));
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $credentials = [
            $fieldType => $login,
            'password' => $request->password
        ];

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
            'login' => 'Email/No HP atau password salah.',
        ])->onlyInput('login');
    }

    public function attemptRegister(Request $request)
    {
        $request->validate([
            'username' => 'required|min:3|max:100',
            'contact' => 'required',
            'password' => 'required|min:6',
            'g-recaptcha-response' => 'required'
        ], [
            'g-recaptcha-response.required' => 'Silakan centang kotak reCAPTCHA untuk membuktikan Anda bukan robot.'
        ]);

        $response = \Illuminate\Support\Facades\Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip()
        ]);

        if (!$response->json('success')) {
            return back()->withErrors(['captcha' => 'Verifikasi reCAPTCHA gagal, silakan coba lagi.'])->withInput();
        }

        $isEmail = filter_var($request->contact, FILTER_VALIDATE_EMAIL);

        if ($isEmail) {
            $request->validate(['contact' => 'unique:users,email']);
        } else {
            $request->validate(['contact' => 'unique:users,phone']);
        }

        $user = User::create([
            'name' => $request->username,
            'email' => $isEmail ? $request->contact : null,
            'phone' => !$isEmail ? $request->contact : null,
            'password' => $request->password, // User model auto-hashes via 'hashed' cast
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
