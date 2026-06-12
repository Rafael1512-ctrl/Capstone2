<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function notice()
    {
        return redirect()->route('login')
            ->with('error', 'Silakan verifikasi email Anda melalui tautan yang dikirim ke inbox.');
    }

    public function verify(Request $request)
    {
        $user = \App\Models\User::findOrFail($request->route('id'));

        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            abort(403, 'Tautan verifikasi tidak valid.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')
                ->with('success', 'Email sudah diverifikasi. Silakan login.');
        }

        if ($user->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($user));
        }

        return redirect()->route('login')
            ->with('success', 'Email berhasil diverifikasi. Silakan login.');
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return back()->with('success', 'Email sudah terverifikasi.');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Tautan verifikasi baru telah dikirim.');
    }
}
