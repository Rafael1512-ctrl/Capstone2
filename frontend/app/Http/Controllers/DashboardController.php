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
}
