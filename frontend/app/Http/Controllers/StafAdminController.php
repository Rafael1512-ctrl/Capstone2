<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class StafAdminController extends Controller
{
    // GET /staf-admin/drafts
    public function showDrafts()
    {
        $data = ApiService::get('/staf-admin/drafts');
        return view('staf_admin.drafts', $data);
    }

    // GET /staf-admin/inventaris
    public function showInventaris()
    {
        $data = ApiService::get('/staf-admin/inventaris');
        return view('staf_admin.inventaris', $data);
    }

    // POST /staf-admin/inventaris/receive/{itemId}
    public function receiveItem(Request $request, $itemId)
    {
        $request->validate([
            'nomor_label'   => 'required|string|max:100',
            'ruangan_id'    => 'required|integer',
            'tanggal_terima'=> 'required|date',
            'kondisi'       => 'required|in:baik,rusak_ringan,rusak_berat',
        ]);
        $res = ApiService::post("/staf-admin/inventaris/receive/{$itemId}", $request->all());
        if (isset($res['error'])) {
            return redirect('/staf-admin/inventaris')->with('error', $res['error']);
        }
        return redirect('/staf-admin/inventaris')->with('success', 'Barang berhasil diterima dan dilabeli.');
    }
}
