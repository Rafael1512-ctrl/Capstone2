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
        // Validate that decisions array exists and has items
        $request->validate([
            'decision' => 'required|array',
            'decision.*' => 'required|in:approved,rejected,pending',
            'catatan_item' => 'nullable|array',
            'alasan_penolakan' => 'nullable|string',
        ]);
        
        try {
            // Forward to API with all data
            $payload = [
                'decision' => $request->input('decision', []),
                'catatan_item' => $request->input('catatan_item', []),
                'alasan_penolakan' => $request->input('alasan_penolakan', ''),
            ];
            
            $response = ApiService::post("/ketua-prodi/review/{$id}/process", $payload);
            
            // Check for API errors
            if (isset($response['error'])) {
                return redirect()->back()->with('error', 'API Error: ' . $response['error']);
            }
            
            return redirect('/ketua-prodi/history')->with('success', 'Keputusan draf berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // GET /ketua-prodi/history
    public function showHistory()
    {
        $data = ApiService::get('/ketua-prodi/history');
        return view('ketua_prodi.history', $data);
    }
}
