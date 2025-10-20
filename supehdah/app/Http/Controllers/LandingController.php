<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClinicInfo;
use App\Models\Service;

class LandingController extends Controller
{
    public function index()
    {
        // Fetch active clinic services
        $services = \App\Models\ClinicService::where('is_active', true)
            ->orderBy('order')
            ->take(6)
            ->get();

        // Fetch ALL clinics with their associated user data, regardless of open status
        $clinics = ClinicInfo::with('user')
            ->orderBy('clinic_name')
            ->get();

        return view('landing', compact('services', 'clinics'));
    }
}
