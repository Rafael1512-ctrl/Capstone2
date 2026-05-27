<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiService
{
    protected static function getBaseUrl()
    {
        return env('NODE_API_URL', 'http://localhost:3000/api');
    }

    protected static function request(string $method, string $endpoint, array $data = [])
    {
        $url = self::getBaseUrl() . '/' . ltrim($endpoint, '/');
        $token = Session::get('jwt_token');

        $pendingRequest = Http::acceptJson();

        if ($token) {
            $pendingRequest = $pendingRequest->withToken($token);
        }

        if (strtoupper($method) === 'GET') {
            $response = $pendingRequest->get($url, $data);
        } else {
            $response = $pendingRequest->post($url, $data);
        }

        if ($response->status() === 401) {
            Session::forget('jwt_token');
            Session::forget('user');
            throw new HttpResponseException(redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.'));
        }

        if ($response->status() === 403) {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        return $response->json();
    }

    public static function get(string $endpoint, array $data = [])
    {
        return self::request('GET', $endpoint, $data);
    }

    public static function post(string $endpoint, array $data = [])
    {
        return self::request('POST', $endpoint, $data);
    }
}
