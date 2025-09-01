<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ClinicInfo;
use Illuminate\Support\Facades\Storage;

class ClinicInfoController extends Controller
{
    public function index()
    {
        $clinics = ClinicInfo::all()->map(function ($clinic) {
            return [
                'id' => $clinic->id,
                'clinic_name' => $clinic->clinic_name,
                'profile_picture' => $clinic->profile_picture 
                    ? asset('storage/' . $clinic->profile_picture)
                    : null,
                'address' => $clinic->address,
                'contact_number' => $clinic->contact_number,
            ];
        });

        return response()->json($clinics);
    }
}