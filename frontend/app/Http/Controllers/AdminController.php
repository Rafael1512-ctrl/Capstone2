<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ApiService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        $response = ApiService::post('/admin/users/create', $request->all());

        if (isset($response['error'])) {
            return back()->withInput()->with('error', $response['error']);
        }

        $user = User::find($response['user_id'] ?? null)
            ?? User::where('email', $request->email)->first();

        $mailMessage = 'Pengguna berhasil ditambahkan.';
        if ($user && ! $user->hasVerifiedEmail()) {
            try {
                event(new Registered($user));
                $mailMessage .= ' Email verifikasi telah dikirim ke ' . $request->email . '.';
                Log::info('Verification email sent on user create', ['user_id' => $user->id, 'email' => $user->email]);
            } catch (\Throwable $e) {
                Log::error('Verification email failed on user create', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                ]);
                $mailMessage .= ' Namun gagal mengirim email verifikasi: ' . $e->getMessage();
            }
        } elseif (! $user) {
            $mailMessage .= ' Namun pengguna tidak ditemukan di database untuk mengirim email verifikasi.';
        }

        return redirect('/admin/users')->with('success', $mailMessage);
    }

    // POST /admin/users/resend-verification/{id}
    public function resendVerification($id)
    {
        $user = User::find($id);

        if (! $user) {
            return redirect('/admin/users')->with('error', 'Pengguna tidak ditemukan.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect('/admin/users')->with('error', 'Email pengguna ini sudah terverifikasi.');
        }

        try {
            $user->sendEmailVerificationNotification();
            Log::info('Verification email resent', ['user_id' => $user->id, 'email' => $user->email]);

            return redirect('/admin/users')->with(
                'success',
                'Email verifikasi berhasil dikirim ulang ke ' . $user->email . '. Cek inbox dan folder spam.'
            );
        } catch (\Throwable $e) {
            Log::error('Verification email resend failed', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);

            return redirect('/admin/users')->with(
                'error',
                'Gagal mengirim email verifikasi: ' . $e->getMessage()
            );
        }
    }

    // POST /admin/users/delete/{id}
    public function deleteUser($id)
    {
        $response = ApiService::post("/admin/users/delete/{$id}");

        if (isset($response['error'])) {
            return redirect('/admin/users')->with('error', $response['error']);
        }

        return redirect('/admin/users')->with('success', 'Pengguna berhasil dihapus.');
    }

    // POST /admin/users/edit/{id}
    public function editUser(Request $request, $id)
    {
        $request->validate([
            'nama'    => 'required|string|max:255',
            'email'   => 'required|email',
            'password'=> 'nullable|min:6',
            'role_id' => 'required|integer',
        ]);

        $response = ApiService::post("/admin/users/edit/{$id}", $request->all());

        if (isset($response['error'])) {
            return back()->withInput()->with('error', $response['error']);
        }

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
