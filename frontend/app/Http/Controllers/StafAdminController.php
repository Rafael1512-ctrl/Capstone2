<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

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
            'tanggal_terima' => 'required|date',
        ]);
        $res = ApiService::post("/staf-admin/inventaris/receive/{$itemId}", $request->all());
        if (isset($res['error'])) {
            return redirect()->back()->with('error', $res['error']);
        }
        return redirect()->back()->with('success', 'Barang berhasil diterima dan dilabeli.');
    }

    // POST /staf-admin/inventaris/upload-qr-univ/{id}
    public function uploadUniversityQr(Request $request, $id)
    {
        $request->validate([
            'qr_univ_file' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'kode_inventaris_univ' => 'nullable|string|max:100',
            'tanggal_daftar_univ' => 'nullable|date',
        ]);

        $payload = [
            'kode_inventaris_univ' => $request->input('kode_inventaris_univ'),
            'tanggal_daftar_univ' => $request->input('tanggal_daftar_univ'),
        ];

        if ($request->hasFile('qr_univ_file')) {
            $file = $request->file('qr_univ_file');
            $extension = $file->getClientOriginalExtension();
            $safeName = 'qr_univ_inventaris_' . $id . '_' . time() . '.' . $extension;
            
            // Ensure public/asset directory exists
            $destinationPath = public_path('asset');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }
            
            // Move file to public/asset
            $file->move($destinationPath, $safeName);
            
            // Set the image path in the payload
            $imagePath = '/asset/' . $safeName;
            
            // Store image path in both fields to satisfy database and UI requirements
            $payload['kode_inventaris_univ'] = $imagePath;
            $payload['qr_univ_path'] = $imagePath;
        }

        $res = ApiService::post("/staf-admin/inventaris/upload-qr-univ/{$id}", $payload);
        if (isset($res['error'])) {
            return redirect()->back()->with('error', $res['error']);
        }

        return redirect()->back()->with('success', $res['message'] ?? 'QR Universitas berhasil diunggah.');
    }

    // POST /staf-admin/inventaris/delete/{id}
    public function deleteInventaris($id)
    {
        ApiService::post("/staf-admin/inventaris/delete/{$id}");
        return redirect()->back()->with('success', 'Barang inventaris berhasil dihapus (soft delete).');
    }
}
