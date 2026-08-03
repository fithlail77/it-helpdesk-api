<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withInput($request->only('email'))->with('error', 'Email atau password salah.');
        }

        if (!$user->is_active) {
            return back()->withInput($request->only('email'))->with('error', 'Akun Anda tidak aktif.');
        }

        session(['web_user' => $user->only(['id', 'name', 'email', 'role', 'team_id', 'phone', 'avatar'])]);

        return redirect()->route('dashboard');
    }

    public function logout()
    {
        session()->forget('web_user');
        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }
}
