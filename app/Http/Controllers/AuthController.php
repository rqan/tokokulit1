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
        $num1 = rand(1, 10);
        $num2 = rand(1, 10);
        session(['captcha_answer' => $num1 + $num2]);
        $captcha_question = "Berapa hasil dari $num1 + $num2 ?";

        return view('auth.register', compact('captcha_question'));
    }

    public function attemptLogin(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required'
        ]);

        $login = $request->input('login');
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
            'captcha' => 'required|numeric'
        ]);

        if ((int)$request->captcha !== (int)session('captcha_answer')) {
            return back()->withErrors(['captcha' => 'Jawaban perhitungan anti-bot salah.'])->withInput();
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
