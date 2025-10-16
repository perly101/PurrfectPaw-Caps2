<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ClinicInfo;
use App\Models\Doctor;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
         * Show the clinic dashboard with statistics.
         *
         * @param \Illuminate\Http\Request $request
         * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
         */
        public function index(Request $request)
    {
        $clinic = ClinicInfo::where('user_id', Auth::id())->first();
        
        if (!$clinic) {
            return redirect()->route('clinic.settings')->with('error', 'Please complete your clinic profile first.');
        }

        // Get doctor count
        $doctorCount = Doctor::where('clinic_id', $clinic->id)->count();
        
        // Get pending appointments
        $pendingAppointmentsCount = Appointment::where('clinic_id', $clinic->id)
            ->where('status', 'pending')
            ->count();
            
        // Get today's appointments
        $todayAppointmentsCount = Appointment::where('clinic_id', $clinic->id)
            ->whereDate('appointment_date', Carbon::today())
            ->count();
            
        // Get total completed appointments
        $completedAppointmentsCount = Appointment::where('clinic_id', $clinic->id)
            ->whereIn('status', ['completed', 'closed'])
            ->count();
            
        // Count users with 'user' role (patients)
        $patientCount = DB::table('users')
            ->where('role', 'user')
            ->count();
            
        // Get time period for graph (default to weekly)
        $period = $request->input('period', 'weekly');
        
        // Get appointment statistics based on period
        $appointmentStats = $this->getAppointmentStats($clinic->id, $period);
        
        return view('clinic.dashboard', compact(
            'clinic',
            'doctorCount',
            'pendingAppointmentsCount',
            'todayAppointmentsCount',
            'completedAppointmentsCount',
            'patientCount',
            'period',
            'appointmentStats'
        ));
    }
    
    /**
     * Get appointment statistics for the selected time period.
     *
     * @param int $clinicId
     * @param string $period
     * @return array
     */
    private function getAppointmentStats($clinicId, $period)
    {
        $labels = [];
        $data = [];
        
        switch ($period) {
            case 'weekly':
                // Get data for the past 7 days
                $startDate = Carbon::now()->subDays(6)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                
                $appointments = Appointment::where('clinic_id', $clinicId)
                    ->whereBetween('appointment_date', [$startDate, $endDate])
                    ->select(DB::raw('DATE(appointment_date) as date'), DB::raw('count(*) as count'))
                    ->groupBy('date')
                    ->get()
                    ->pluck('count', 'date')
                    ->toArray();
                
                // Create the date range
                $period = CarbonPeriod::create($startDate, $endDate);
                
                foreach ($period as $date) {
                    $formattedDate = $date->format('Y-m-d');
                    $labels[] = $date->format('D, M j'); // e.g., "Mon, Jan 1"
                    $data[] = $appointments[$formattedDate] ?? 0;
                }
                break;
                
            case 'monthly':
                // Get data for the past 30 days grouped by week
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                
                $appointments = Appointment::where('clinic_id', $clinicId)
                    ->whereBetween('appointment_date', [$startDate, $endDate])
                    ->select(DB::raw('YEARWEEK(appointment_date, 1) as week'), DB::raw('count(*) as count'))
                    ->groupBy('week')
                    ->get()
                    ->pluck('count', 'week')
                    ->toArray();
                
                // Create weeks
                $currentDate = Carbon::parse($startDate);
                while ($currentDate <= $endDate) {
                    $weekNum = $currentDate->format('oW');
                    $weekStart = Carbon::parse($currentDate)->startOfWeek();
                    $weekEnd = Carbon::parse($currentDate)->endOfWeek();
                    
                    $labels[] = $weekStart->format('M j') . ' - ' . $weekEnd->format('M j');
                    $data[] = $appointments[$weekNum] ?? 0;
                    
                    $currentDate->addWeek();
                }
                break;
                
            case 'yearly':
                // Get data for the past 12 months
                $months = [];
                for ($i = 11; $i >= 0; $i--) {
                    $months[] = Carbon::now()->subMonths($i);
                }
                
                foreach ($months as $month) {
                    $monthStart = Carbon::parse($month)->startOfMonth();
                    $monthEnd = Carbon::parse($month)->endOfMonth();
                    
                    $count = Appointment::where('clinic_id', $clinicId)
                        ->whereBetween('appointment_date', [$monthStart, $monthEnd])
                        ->count();
                    
                    $labels[] = $month->format('M Y'); // e.g., "Jan 2023"
                    $data[] = $count;
                }
                break;
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
    
    /**
     * AJAX endpoint to get appointment statistics for a specific period.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats(Request $request)
    {
        $clinic = ClinicInfo::where('user_id', Auth::id())->first();
        
        if (!$clinic) {
            return response()->json(['error' => 'Clinic not found'], 404);
        }
        
        $period = $request->input('period', 'weekly');
        $stats = $this->getAppointmentStats($clinic->id, $period);
        
        return response()->json($stats);
    }
}