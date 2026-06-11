<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Exceptions\HttpResponseException;
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
                $errorType = ($response['code'] ?? '') === 'EMAIL_NOT_VERIFIED' ? 'unverified' : 'auth';

                return back()
                    ->withInput($request->only('email'))
                    ->with('error', $response['error'])
                    ->with('error_type', $errorType);
            }

            if (empty($response['token'])) {
                return back()
                    ->withInput($request->only('email'))
                    ->with('error', 'Login gagal. Pastikan email sudah diverifikasi.')
                    ->with('error_type', 'unverified');
            }

            Session::put('jwt_token', $response['token']);
            Session::put('user', $response['user']);

            return redirect()->route('dashboard');
        } catch (HttpResponseException $e) {
            throw $e;
        } catch (ConnectionException $e) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Gagal terhubung ke server. Pastikan backend Node.js berjalan di port 3000.')
                ->with('error_type', 'connection');
        } catch (\Exception $e) {
            report($e);

            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Terjadi kesalahan saat login. Silakan coba lagi.')
                ->with('error_type', 'server');
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
