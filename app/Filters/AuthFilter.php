<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Cek apakah user sudah login
        if (!$session->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // **Deteksi Perubahan IP Address atau User-Agent (Session Hijacking Protection)**
        $currentIP = $request->getIPAddress();
        $currentAgent = $request->getUserAgent()->getAgentString();

        if ($session->get('ip_address') !== $currentIP || $session->get('user_agent') !== $currentAgent) {
            // Jika IP atau User-Agent berbeda, logout user dan hancurkan sesi
            $session->destroy();
            return redirect()->to('/login')->with('error', 'Sesi Anda tidak valid! Silakan login kembali.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak diperlukan
    }
}
