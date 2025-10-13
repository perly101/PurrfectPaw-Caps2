<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Clinic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ClinicNotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated clinic.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = Auth::user();
        $clinic = $user->clinic;
        
        if (!$clinic) {
            return response()->json([
                'success' => false,
                'message' => 'No clinic associated with this user'
            ], 404);
        }
        
        // Get notifications for the clinic
        $notifications = $clinic->notifications()
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }
    
    /**
     * Mark a notification as read.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $clinic = $user->clinic;
        
        if (!$clinic) {
            return response()->json([
                'success' => false,
                'message' => 'No clinic associated with this user'
            ], 404);
        }
        
        $notification = $clinic->notifications()->findOrFail($id);
        $notification->read_at = now();
        $notification->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }
    
    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $clinic = $user->clinic;
        
        if (!$clinic) {
            return response()->json([
                'success' => false,
                'message' => 'No clinic associated with this user'
            ], 404);
        }
        
        $clinic->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
            
        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }
    
    /**
     * Delete a notification.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $clinic = $user->clinic;
        
        if (!$clinic) {
            return response()->json([
                'success' => false,
                'message' => 'No clinic associated with this user'
            ], 404);
        }
        
        $notification = $clinic->notifications()->findOrFail($id);
        $notification->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully'
        ]);
    }
    
    /**
     * Get unread notifications count.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        $clinic = $user->clinic;
        
        if (!$clinic) {
            return response()->json([
                'success' => false,
                'message' => 'No clinic associated with this user'
            ], 404);
        }
        
        $count = $clinic->notifications()
            ->whereNull('read_at')
            ->count();
            
        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }
}