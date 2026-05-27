<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // GET /admin/users
    public function showUsers()
    {
        $data = ApiService::get('/admin/users');
        return view('admin.users', $data);
    }

    // POST /admin/users/create
    public function createUser(Request $request)
    {
        $request->validate([
            'nama'    => 'required|string|max:255',
            'email'   => 'required|email',
            'password'=> 'required|min:6',
            'role_id' => 'required|integer',
        ]);
        ApiService::post('/admin/users/create', $request->all());
        return redirect('/admin/users')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    // POST /admin/users/delete/{id}
    public function deleteUser($id)
    {
        ApiService::post("/admin/users/delete/{$id}");
        return redirect('/admin/users')->with('success', 'Pengguna berhasil dihapus.');
    }

    // POST /admin/users/edit/{id}
    public function editUser(Request $request, $id)
    {
        ApiService::post("/admin/users/edit/{$id}", $request->all());
        return redirect('/admin/users')->with('success', 'Pengguna berhasil diperbarui.');
    }

    // GET /admin/ruangan
    public function showRuangan()
    {
        $data = ApiService::get('/admin/ruangan');
        return view('admin.ruangan', $data);
    }

    // POST /admin/ruangan/create
    public function createRuangan(Request $request)
    {
        $request->validate([
            'nama_ruangan'  => 'required|string|max:255',
            'kode_ruangan'  => 'required|string|max:50',
            'lokasi'        => 'required|string|max:255',
        ]);
        ApiService::post('/admin/ruangan/create', $request->all());
        return redirect('/admin/ruangan')->with('success', 'Ruangan berhasil ditambahkan.');
    }

    // POST /admin/ruangan/delete/{id}
    public function deleteRuangan($id)
    {
        ApiService::post("/admin/ruangan/delete/{$id}");
        return redirect('/admin/ruangan')->with('success', 'Ruangan berhasil dihapus.');
    }

    // POST /admin/ruangan/edit/{id}
    public function editRuangan(Request $request, $id)
    {
        ApiService::post("/admin/ruangan/edit/{$id}", $request->all());
        return redirect('/admin/ruangan')->with('success', 'Ruangan berhasil diperbarui.');
    }
}
