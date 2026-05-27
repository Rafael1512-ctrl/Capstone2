<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    // GET /login
    public function showLogin()
    {
        if (Session::has('jwt_token')) {
            return redirect()->route('dashboard');
        }
        return view('auth.signin');
    }

    // POST /login
    public function handleLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        try {
            $response = ApiService::post('auth/login', [
                'email'    => $request->email,
                'password' => $request->password,
            ]);

            if (isset($response['error'])) {
                return back()->with('error', $response['error']);
            }

            Session::put('jwt_token', $response['token']);
            Session::put('user', $response['user']);

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal terhubung ke server. Pastikan backend berjalan.');
        }
    }

    // POST /logout
    public function handleLogout()
    {
        try {
            ApiService::post('auth/logout');
        } catch (\Exception $e) {
            // Tetap lanjut logout meski API gagal
        }

        Session::forget('jwt_token');
        Session::forget('user');

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
