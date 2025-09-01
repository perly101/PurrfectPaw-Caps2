<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\ClinicInfo;
use Illuminate\Http\Request;

class PublicCalendarController extends Controller
{
    /**
     * Display public availability calendar for a clinic
     *
     * @param  Request  $request
     * @param  int|null $clinicId
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request, $clinicId = null)
    {
        // If no clinic ID provided, use the clinic ID from the request
        if (!$clinicId) {
            $clinicId = $request->clinic_id;
        }
        
        // Get the clinic information
        $clinic = ClinicInfo::find($clinicId);
        
        // If clinic not found, show error
        if (!$clinic) {
            return view('errors.clinic-not-found');
        }
        
        return view('clinic.public-calendar', [
            'clinic' => $clinic
        ]);
    }
}
