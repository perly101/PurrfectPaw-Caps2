<?php

namespace Database\Seeders;

use App\Models\CustomField;
use App\Models\ClinicInfo;
use Illuminate\Database\Seeder;

class CustomFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all clinic IDs
        $clinics = ClinicInfo::all();
        
        // Default pet types
        $petTypes = ['Dog', 'Cat', 'Bird', 'Rabbit', 'Hamster', 'Guinea Pig'];
        
        // Default treatment types
        $treatments = ['Check-up', 'Vaccination', 'Grooming', 'Surgery', 'Dental Cleaning', 'Emergency Care'];
        
        foreach ($clinics as $clinic) {
            // Add pet types for this clinic
            foreach ($petTypes as $petType) {
                CustomField::create([
                    'clinic_id' => $clinic->id,
                    'type' => 'pet',
                    'value' => $petType
                ]);
            }
            
            // Add treatment types for this clinic
            foreach ($treatments as $treatment) {
                CustomField::create([
                    'clinic_id' => $clinic->id,
                    'type' => 'treatment',
                    'value' => $treatment
                ]);
            }
        }
    }
}
