<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class KetuaProdiController extends Controller
{
    // GET /ketua-prodi/review
    public function showReview()
    {
        $data = ApiService::get('/ketua-prodi/review');
        return view('ketua_prodi.review', $data);
    }

    // GET /ketua-prodi/review/{id}
    public function showReviewDetail($id)
    {
        $data = ApiService::get("/ketua-prodi/review/{$id}");
        return view('ketua_prodi.detail', $data);
    }

    // POST /ketua-prodi/review/{id}/process
    public function processDraft(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);
        ApiService::post("/ketua-prodi/review/{$id}/process", $request->all());
        return redirect('/ketua-prodi/history')->with('success', 'Keputusan draf berhasil disimpan.');
    }

    // GET /ketua-prodi/history
    public function showHistory()
    {
        $data = ApiService::get('/ketua-prodi/history');
        return view('ketua_prodi.history', $data);
    }
}
