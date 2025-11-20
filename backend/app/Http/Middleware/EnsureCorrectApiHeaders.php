<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCorrectApiHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the response
        $response = $next($request);
        
        // If this is an OPTIONS request, handle CORS preflight
        if ($request->isMethod('OPTIONS')) {
            return $this->addCorsHeaders($response);
        }
        
        // Ensure the response has the correct content type
        if (!$response->headers->has('Content-Type')) {
            $response->header('Content-Type', 'application/json');
        }
        
        // Add CORS headers to all responses
        return $this->addCorsHeaders($response);
    }
    
    /**
     * Add CORS headers to the response.
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function addCorsHeaders($response)
    {
        // Get the app URL without the protocol
        $appUrl = config('app.url');
        $origin = parse_url($appUrl, PHP_URL_HOST);
        
        // Set CORS headers
        $response->headers->set('Access-Control-Allow-Origin', $appUrl);
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Vary', 'Origin');
        
        return $response;
    }
}
