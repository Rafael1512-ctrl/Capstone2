<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // GET /
    public function index()
    {
        $data = ApiService::get('/');
        return view('dashboard.index', $data);
    }

    // GET /inventory
    public function inventory()
    {
        $data = ApiService::get('/inventory');
        return view('dashboard.inventory', $data);
    }

    // GET /create-product
    public function showCreateProduct()
    {
        $data = ApiService::get('/create-product');
        return view('dashboard.create-product', $data);
    }

    // POST /create-product
    public function handleCreateProduct(Request $request)
    {
        ApiService::post('/create-product', $request->all());
        return redirect('/inventory')->with('success', 'Produk berhasil ditambahkan.');
    }

    // GET /reports
    public function reports()
    {
        $data = ApiService::get('/reports');
        return view('dashboard.reports', $data);
    }

    // GET /docs
    public function docs()
    {
        $data = ApiService::get('/docs');
        return view('dashboard.docs', $data);
    }

    // GET /api-notifications
    public function getNotifications()
    {
        $response = ApiService::get('/notifications');
        return response()->json($response);
    }

    // GET /profile
    public function showProfile()
    {
        $user = \Illuminate\Support\Facades\Session::get('user');
        return view('profile', [
            'title' => 'Profil Saya',
            'activePath' => '/profile',
            'user' => $user
        ]);
    }

    // POST /profile
    public function updateProfile(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $payload = [
            'nama' => $request->input('nama'),
            'email' => $request->input('email'),
            'password' => $request->input('password')
        ];

        $response = ApiService::post('/profile/update', $payload);

        if (isset($response['error'])) {
            return redirect()->back()->withInput()->with('error', $response['error']);
        }

        if (isset($response['token'])) {
            \Illuminate\Support\Facades\Session::put('jwt_token', $response['token']);
        }
        if (isset($response['user'])) {
            \Illuminate\Support\Facades\Session::put('user', $response['user']);
        }

        return redirect()->back()->with('success', 'Profil Anda berhasil diperbarui.');
    }
}
