<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCanonicalUrl
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya untuk halaman HTML
        if ($response->headers->get('Content-Type') && 
            strpos($response->headers->get('Content-Type'), 'text/html') !== false) {
            
            // Generate canonical URL (tanpa query string)
            $canonicalUrl = url()->current();
            
            // Simpan ke view composer agar bisa diakses di layout
            view()->share('canonicalUrl', $canonicalUrl);
        }

        return $response;
    }
}
