<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Maintenance implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Cek apakah mode maintenance aktif
        $maintenance = env('maintenance.enabled', false);
        if (!$maintenance) {
            return;
        }

        // Dapatkan URI saat ini
        $currentUri = $request->getUri()->getPath();

        // Bersihkan URI: hapus 'index.php/' dan slash di awal
        $cleanUri = preg_replace('/^\/?index\.php\//', '', $currentUri);
        $cleanUri = ltrim($cleanUri, '/');

        // Daftar PREFIX URI yang BOLEH DIAKSES PUBLIK
        $publicPrefixes = [
            //'',           // root
            'login',
            //'lacak-mutasi',
            'auth',       // semua route auth (login, authenticate, logout, dll)
        ];

        // Periksa apakah URI diawali dengan salah satu prefix publik
        foreach ($publicPrefixes as $prefix) {
            // Jika prefix kosong (root), cocokkan jika URI juga kosong
            if ($prefix === '' && $cleanUri === '') {
                return; // izinkan root
            }
            // Untuk prefix lainnya, cek apakah URI dimulai dengan prefix/
            if ($prefix !== '' && strpos($cleanUri, $prefix . '/') === 0) {
                return; // izinkan semua di bawah prefix tersebut
            }
            // Atau URI persis sama dengan prefix
            if ($cleanUri === $prefix) {
                return; // izinkan halaman utama prefix
            }
        }

        // Cek apakah user adalah admin (bisa akses semua)
        $role = session()->get('role');
        if ($role === 'admin') {
            return; // admin tetap bisa akses semua
        }

        // Selain admin dan bukan halaman publik, tampilkan maintenance
        $response = service('response');
        $response->setBody(view('maintenance'));
        return $response;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu
    }
}