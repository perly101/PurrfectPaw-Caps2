<?php

namespace App\Http\Controllers;

use App\Models\ClinicInfo;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GlobalNotificationController extends Controller
{
    /**
     * Check for new notifications - accessible globally, not just in clinic routes
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkNewNotifications(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
            ], 401);
        }
        
        $user = Auth::user();
        
        // For clinic users, check notifications
        if ($user->role === 'clinic') {
            $clinic = ClinicInfo::where('user_id', $user->id)->first();
            
            if (!$clinic) {
                return response()->json([
                    'success' => false, 
                    'message' => 'No clinic found for this user'
                ], 404);
            }
            
            $since = $request->query('since');
            $sinceDate = $since ? new \DateTime($since) : null;
            
            $query = $clinic->notifications()->whereNull('read_at');
            
            if ($sinceDate) {
                $query->where('created_at', '>', $sinceDate);
            }
            
            $notifications = $query->orderBy('created_at', 'desc')->get();
            
            return response()->json([
                'success' => true,
                'now' => now()->toISOString(),
                'count' => $notifications->count(),
                'notifications' => $notifications
            ]);
        }
        
        // For doctor users (can be implemented similarly)
        if ($user->role === 'doctor') {
            // Doctor notification code here
            return response()->json([
                'success' => true,
                'now' => now()->toISOString(),
                'count' => 0,
                'notifications' => []
            ]);
        }
        
        // Default empty response for other user types
        return response()->json([
            'success' => true,
            'now' => now()->toISOString(),
            'count' => 0,
            'notifications' => []
        ]);
    }
    
    /**
     * Get notification count
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNotificationCount()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
            ], 401);
        }
        
        $user = Auth::user();
        $count = 0;
        
        // For clinic users
        if ($user->role === 'clinic') {
            $clinic = ClinicInfo::where('user_id', $user->id)->first();
            
            if ($clinic) {
                $count = $clinic->notifications()->whereNull('read_at')->count();
            }
        }
        
        // For doctor users (can be implemented similarly)
        if ($user->role === 'doctor') {
            // Get doctor notification count
        }
        
        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }
    
    /**
     * Mark a notification as read
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
            ], 401);
        }
        
        $user = Auth::user();
        
        // For clinic users
        if ($user->role === 'clinic') {
            $clinic = ClinicInfo::where('user_id', $user->id)->first();
            
            if (!$clinic) {
                return response()->json([
                    'success' => false,
                    'message' => 'No clinic found for this user'
                ], 404);
            }
            
            $notification = $clinic->notifications()->find($id);
            
            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }
            
            $notification->read_at = now();
            $notification->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Unsupported user role'
        ], 400);
    }
    
    /**
     * Mark all notifications as read
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
            ], 401);
        }
        
        $user = Auth::user();
        
        // For clinic users
        if ($user->role === 'clinic') {
            $clinic = ClinicInfo::where('user_id', $user->id)->first();
            
            if (!$clinic) {
                return response()->json([
                    'success' => false,
                    'message' => 'No clinic found for this user'
                ], 404);
            }
            
            $clinic->notifications()->whereNull('read_at')->update(['read_at' => now()]);
            
            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Unsupported user role'
        ], 400);
    }
}