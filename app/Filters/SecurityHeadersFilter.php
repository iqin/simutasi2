<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class SecurityHeadersFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Tidak ada aksi sebelum request
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $response->setHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->setHeader('X-Content-Type-Options', 'nosniff');
        $response->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->setHeader('X-XSS-Protection', '1; mode=block');

        $response->setHeader(
            'Strict-Transport-Security',
            'max-age=31536000; includeSubDomains; preload'
        );

        // ✅ Content Security Policy (CSP) final dengan dukungan Google reCAPTCHA
        $response->setHeader(
            'Content-Security-Policy',
            "default-src 'self'; img-src 'self' data: https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.gstatic.com https://www.gstatic.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com https://fonts.gstatic.com; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://kit.fontawesome.com https://www.google.com https://www.gstatic.com; font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; connect-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://www.google.com https://www.gstatic.com; frame-src 'self' https://www.google.com https://www.gstatic.com;"
        );

        $response->setHeader(
            'Permissions-Policy',
            'geolocation=(), microphone=(), camera=(), fullscreen=(self)'
        );

        $response->setHeader('Cross-Origin-Embedder-Policy', 'require-corp');
        $response->setHeader('Cross-Origin-Opener-Policy', 'same-origin');
        $response->setHeader('Cross-Origin-Resource-Policy', 'same-origin');

        return $response;
    }

}
