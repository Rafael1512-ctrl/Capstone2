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

    // GET /inventaris
    public function showInventaris(Request $request)
    {
        $draftId = $request->query('draft_id');
        $params = [];
        if ($draftId) {
            $params['draft_id'] = $draftId;
        }
        $data = ApiService::get('/inventaris', $params);
        $data['selectedDraftId'] = $draftId;
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
            return redirect()->back()->with('error', $res['error']);
        }
        return redirect()->back()->with('success', 'Barang berhasil diterima dan dilabeli.');
    }

    // POST /staf-admin/inventaris/delete/{id}
    public function deleteInventaris($id)
    {
        ApiService::post("/staf-admin/inventaris/delete/{$id}");
        return redirect()->back()->with('success', 'Barang inventaris berhasil dihapus (soft delete).');
    }
}
