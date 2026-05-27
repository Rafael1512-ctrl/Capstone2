<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class StafLabController extends Controller
{
    // GET /staf-lab/bhp
    public function showBHP()
    {
        $data = ApiService::get('/staf-lab/bhp');
        return view('staf_lab.bhp', $data);
    }

    // POST /staf-lab/bhp/create
    public function createBHP(Request $request)
    {
        $request->validate([
            'nama_bhp'   => 'required|string|max:255',
            'ruangan_id' => 'required|integer',
            'stok'       => 'required|integer|min:0',
            'satuan'     => 'required|string|max:50',
            'kondisi'    => 'required|in:baik,rusak',
        ]);
        ApiService::post('/staf-lab/bhp/create', $request->all());
        return redirect('/staf-lab/bhp')->with('success', 'BHP berhasil ditambahkan.');
    }

    // POST /staf-lab/bhp/update-stock/{id}
    public function updateBHPStock(Request $request, $id)
    {
        $request->validate([
            'stok'   => 'required|integer|min:0',
            'kondisi'=> 'required|in:baik,rusak',
        ]);
        ApiService::post("/staf-lab/bhp/update-stock/{$id}", $request->all());
        return redirect('/staf-lab/bhp')->with('success', 'Stok BHP berhasil diperbarui.');
    }

    // GET /staf-lab/maintenance
    public function showMaintenance()
    {
        $data = ApiService::get('/staf-lab/maintenance');
        return view('staf_lab.maintenance', $data);
    }

    // POST /staf-lab/maintenance/create
    public function createMaintenance(Request $request)
    {
        $request->validate([
            'inventaris_id' => 'required|integer',
            'deskripsi'     => 'required|string',
            'status_akhir'  => 'required|in:baik,perlu_perbaikan,rusak',
        ]);
        ApiService::post('/staf-lab/maintenance/create', $request->all());
        return redirect('/staf-lab/maintenance')->with('success', 'Log maintenance berhasil dicatat.');
    }
}
