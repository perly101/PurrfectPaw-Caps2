<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RefreshAppointmentData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // For API responses, add headers to prevent caching of appointment data
        if ($this->isAppointmentEndpoint($request) && $response->headers->get('Content-Type') === 'application/json') {
            $response->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->header('Pragma', 'no-cache');
            $response->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        }
        
        return $response;
    }
    
    /**
     * Determine if the request is for an appointment-related endpoint.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    private function isAppointmentEndpoint(Request $request)
    {
        $path = $request->path();
        return strpos($path, 'api/clinic') !== false && 
               (strpos($path, 'availability') !== false || 
                strpos($path, 'appointments') !== false);
    }
}
