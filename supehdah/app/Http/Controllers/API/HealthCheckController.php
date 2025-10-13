<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HealthCheckController extends Controller
{
    /**
     * A simple health check endpoint to verify API is accessible
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function check()
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
            'message' => 'API is running',
            'environment' => app()->environment(),
        ]);
    }
}