<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OptimizeResponse
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Habilitar compresión GZIP
        if (!$response->headers->has('Content-Encoding')) {
            $content = $response->getContent();
            if (strlen($content) > 1000 && function_exists('gzencode')) {
                $compressed = gzencode($content, 6);
                $response->setContent($compressed);
                $response->headers->set('Content-Encoding', 'gzip');
                $response->headers->set('Vary', 'Accept-Encoding');
            }
        }
        
        // Cache headers para recursos estáticos
        if ($request->is('*.css') || $request->is('*.js') || $request->is('*.png') || $request->is('*.jpg')) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000');
        }
        
        return $response;
    }
}