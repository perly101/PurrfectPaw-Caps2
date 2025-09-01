<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\ClinicInfo;
use Illuminate\Http\Request;

class CustomFieldApiController extends Controller
{
    /**
     * Get custom fields for a clinic
     *
     * @param int $clinicId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCustomFields($clinicId)
    {
        $clinic = ClinicInfo::findOrFail($clinicId);
        
        $petOptions = CustomField::where('clinic_id', $clinicId)
            ->where('type', 'pet')
            ->pluck('value')
            ->toArray();
            
        $treatmentOptions = CustomField::where('clinic_id', $clinicId)
            ->where('type', 'treatment')
            ->pluck('value')
            ->toArray();
        
        return response()->json([
            'data' => [
                'petOptions' => $petOptions,
                'treatmentOptions' => $treatmentOptions
            ]
        ]);
    }
}
