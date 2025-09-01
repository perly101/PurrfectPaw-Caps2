<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ClinicInfo;
use Illuminate\Http\Request;

class ClinicStatusController extends Controller
{
    /**
     * Get the open/closed status of a specific clinic.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatus($id)
    {
        $clinic = ClinicInfo::find($id);
        
        if (!$clinic) {
            return response()->json([
                'success' => false,
                'message' => 'Clinic not found',
                'is_open' => false
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'clinic_id' => $clinic->id,
            'clinic_name' => $clinic->clinic_name,
            'is_open' => (bool) $clinic->is_open
        ]);
    }
}
