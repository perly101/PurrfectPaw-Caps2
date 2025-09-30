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
        $type = $request->input('type'); // New filter for log type
        $userType = $request->input('user_type'); // New filter for user type
        $dateRange = $request->input('date_range'); // New filter for date range
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        
        $query = SystemLog::with('user')->latest();
        
        // Apply filters
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('details', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        // Handle log type filter
        if ($type) {
            switch ($type) {
                case 'login':
                    $query->where('action', 'like', '%login%');
                    break;
                case 'data':
                    $query->where('action', 'like', '%update%')
                         ->orWhere('action', 'like', '%create%')
                         ->orWhere('action', 'like', '%delete%');
                    break;
                case 'system':
                    $query->where('action', 'like', '%system%');
                    break;
            }
        }
        
        // Handle user type filter
        if ($userType) {
            if ($userType === 'system') {
                $query->whereNull('user_id');
            } else {
                $query->whereHas('user', function($q) use ($userType) {
                    $q->where('role', $userType);
                });
            }
        }
        
        // Handle date range filter
        if ($dateRange) {
            $now = now();
            switch ($dateRange) {
                case 'today':
                    $query->whereDate('created_at', $now->format('Y-m-d'));
                    break;
                case 'week':
                    $query->where('created_at', '>=', $now->subDays(7));
                    break;
                case 'month':
                    $query->where('created_at', '>=', $now->subDays(30));
                    break;
            }
        } else {
            // Use explicit date range if provided
            if ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }
            
            if ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            }
        }
        
        $logs = $query->paginate(15);
        $logs->appends($request->all());
        
        return view('admin.logs', compact('logs'));
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
