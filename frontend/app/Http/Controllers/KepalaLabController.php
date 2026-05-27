<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class KepalaLabController extends Controller
{
    // GET /kepala-lab/pengadaan
    public function showPengadaan()
    {
        $data = ApiService::get('/kepala-lab/pengadaan');
        return view('kepala_lab.pengadaan', $data);
    }

    // POST /kepala-lab/pengadaan/create-draft
    public function createDraft(Request $request)
    {
        $request->validate([
            'tahun'          => 'required|integer|min:2000|max:2099',
            'ketua_prodi_id' => 'required|integer',
        ]);
        ApiService::post('/kepala-lab/pengadaan/create-draft', $request->all());
        return redirect('/kepala-lab/pengadaan')->with('success', 'Draf berhasil dibuat.');
    }

    // POST /kepala-lab/pengadaan/update-draft/{id}
    public function updateDraft(Request $request, $id)
    {
        ApiService::post("/kepala-lab/pengadaan/update-draft/{$id}", $request->all());
        return redirect('/kepala-lab/pengadaan')->with('success', 'Draf berhasil diperbarui.');
    }

    // POST /kepala-lab/pengadaan/delete-draft/{id}
    public function deleteDraft($id)
    {
        ApiService::post("/kepala-lab/pengadaan/delete-draft/{$id}");
        return redirect('/kepala-lab/pengadaan')->with('success', 'Draf berhasil dihapus.');
    }

    // POST /kepala-lab/pengadaan/add-item
    public function addItem(Request $request)
    {
        $request->validate([
            'draft_id'     => 'required|integer',
            'nama_barang'  => 'required|string|max:255',
            'tipe_barang'  => 'required|in:inventaris,bhp',
            'harga_satuan' => 'required|numeric|min:0',
            'jumlah'       => 'required|integer|min:1',
            'rasionalisasi'=> 'required|string',
        ]);
        ApiService::post('/kepala-lab/pengadaan/add-item', $request->all());
        return redirect('/kepala-lab/pengadaan')->with('success', 'Item berhasil ditambahkan ke draf.');
    }

    // POST /kepala-lab/pengadaan/delete-item/{id}
    public function deleteItem($id)
    {
        ApiService::post("/kepala-lab/pengadaan/delete-item/{$id}");
        return redirect('/kepala-lab/pengadaan')->with('success', 'Item berhasil dihapus dari draf.');
    }

    // POST /kepala-lab/pengadaan/submit/{id}
    public function submitDraft($id)
    {
        ApiService::post("/kepala-lab/pengadaan/submit/{$id}");
        return redirect('/kepala-lab/history')->with('success', 'Draf berhasil dikirim ke Ketua Prodi.');
    }

    // GET /kepala-lab/history
    public function showHistory()
    {
        $data = ApiService::get('/kepala-lab/history');
        return view('kepala_lab.history', $data);
    }
}
