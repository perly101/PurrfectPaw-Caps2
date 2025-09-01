<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClinicInfo;
use Illuminate\Support\Facades\Storage;

class ClinicController extends Controller
{
    public function index()
    {
        $clinics = ClinicInfo::query()
            ->select('id', 'clinic_name', 'address', 'contact_number', 'logo', 'profile_picture', 'is_open')
            ->orderBy('clinic_name')
            ->get()
            ->map(function ($clinic) {
                return [
                    'id' => $clinic->id,
                    'clinic_name' => $clinic->clinic_name,
                    'address' => $clinic->address,
                    'contact_number' => $clinic->contact_number,
                    'logo' => $clinic->logo,
                    'profile_picture' => $clinic->profile_picture,
                    'is_open' => (bool) $clinic->is_open,
                    'image_url' => $clinic->logo ? asset('storage/' . $clinic->logo) : 
                                  ($clinic->profile_picture ? asset('storage/' . $clinic->profile_picture) : null),
                ];
            });
            
        return response()->json(['data' => $clinics]);
    }
}