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

        $pendingRequest = Http::acceptJson()->timeout(15);

        if ($token) {
            $pendingRequest = $pendingRequest->withToken($token);
        }

        if (strtoupper($method) === 'GET') {
            $response = $pendingRequest->get($url, $data);
        } else {
            $response = $pendingRequest->post($url, $data);
        }

        $body = $response->json();

        if ($response->status() === 401) {
            if ($token) {
                Session::forget('jwt_token');
                Session::forget('user');
                throw new HttpResponseException(
                    redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.')
                );
            }

            return is_array($body) ? $body : ['error' => 'Unauthorized'];
        }

        if (in_array($response->status(), [400, 403, 404, 422], true)) {
            if (is_array($body) && isset($body['error'])) {
                return $body;
            }

            if ($response->status() === 403) {
                abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
            }
        }

        if (! $response->successful()) {
            return [
                'error' => is_array($body) ? ($body['error'] ?? 'Terjadi kesalahan pada server.') : 'Terjadi kesalahan pada server.',
            ];
        }

        return is_array($body) ? $body : [];
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
