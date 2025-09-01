<?php

namespace App\Http\Controllers;

use App\Models\SystemLog;
use Illuminate\Http\Request;

class SystemLogController extends Controller
{
    /**
     * Display a listing of the system logs
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        
        $query = SystemLog::with('user')->latest();
        
        // Apply filters
        if ($search) {
            $query->where('action', 'like', "%{$search}%");
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        
        $logs = $query->paginate(15);
        $logs->appends($request->all());
        
        return view('admin.system_logs', compact('logs'));
    }
    
    /**
     * Clear system logs
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear(Request $request)
    {
        // Optional: Only clear logs with certain criteria
        $status = $request->input('status');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        
        $query = SystemLog::query();
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        
        $deleted = $query->delete();
        
        // Log this action
        SystemLog::log(
            'Cleared system logs',
            [
                'deleted_count' => $deleted,
                'filters' => [
                    'status' => $status,
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                ]
            ],
            'info'
        );
        
        return redirect()->route('admin.system-logs')->with('success', 'System logs cleared successfully');
    }
    
    /**
     * Export system logs as CSV
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function export(Request $request)
    {
        $status = $request->input('status');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        
        $query = SystemLog::with('user')->latest();
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        
        $logs = $query->get();
        
        // Log this export action
        SystemLog::log(
            'Exported system logs',
            [
                'count' => $logs->count(),
                'filters' => [
                    'status' => $status,
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                ]
            ],
            'info'
        );
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="system-logs-' . date('Y-m-d') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];
        
        $callback = function() use ($logs) {
            $handle = fopen('php://output', 'w');
            
            // Add CSV header
            fputcsv($handle, ['ID', 'User', 'Action', 'IP Address', 'Status', 'Date/Time', 'Details']);
            
            foreach ($logs as $log) {
                $row = [
                    $log->id,
                    $log->user ? $log->user->first_name . ' ' . $log->user->last_name . ' (' . $log->user->email . ')' : 'System',
                    $log->action,
                    $log->ip_address,
                    $log->status,
                    $log->created_at->format('Y-m-d H:i:s'),
                    json_encode($log->details),
                ];
                
                fputcsv($handle, $row);
            }
            
            fclose($handle);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
