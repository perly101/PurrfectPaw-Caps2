<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\ClinicInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|\Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $clinic = ClinicInfo::where('user_id', auth()->id())->first();
        
        if (!$clinic) {
            return redirect()->route('clinic.dashboard')
                ->with('error', 'No clinic found for this user.');
        }
        
        $notifications = $clinic->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('clinic.notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsRead($id)
    {
        $clinic = ClinicInfo::where('user_id', auth()->id())->first();
        
        if (!$clinic) {
            return redirect()->back()->with('error', 'No clinic found for this user.');
        }
        
        $notification = $clinic->notifications()->findOrFail($id);
        $notification->read_at = now();
        $notification->save();
        
        return redirect()->back()->with('success', 'Notification marked as read.');
    }
    
    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAllAsRead()
    {
        $clinic = ClinicInfo::where('user_id', auth()->id())->first();
        
        if (!$clinic) {
            return redirect()->back()->with('error', 'No clinic found for this user.');
        }
        
        $clinic->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
            
        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $clinic = ClinicInfo::where('user_id', auth()->id())->first();
        
        if (!$clinic) {
            return redirect()->back()->with('error', 'No clinic found for this user.');
        }
        
        $notification = $clinic->notifications()->findOrFail($id);
        $notification->delete();
        
        return redirect()->back()->with('success', 'Notification deleted successfully.');
    }
    
    /**
     * Check for new notifications since a certain time
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkNewNotifications(Request $request)
    {
        $clinic = ClinicInfo::where('user_id', auth()->id())->first();
        
        if (!$clinic) {
            return response()->json(['error' => 'No clinic found for this user'], 404);
        }
        
        $since = $request->query('since');
        $sinceDate = $since ? new \DateTime($since) : null;
        
        $query = $clinic->notifications();
        
        if ($sinceDate) {
            $query->where('created_at', '>', $sinceDate);
        }
        
        $notifications = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'count' => $notifications->count(),
            'notifications' => $notifications
        ]);
    }
    
    /**
     * Get the notifications component HTML
     *
     * @return \Illuminate\View\View
     */
    public function getNotificationsComponent()
    {
        return view('clinic.components.notifications');
    }
    
    /**
     * Display notification settings page
     *
     * @return \Illuminate\View\View
     */
    public function settings()
    {
        return view('clinic.notifications.settings');
    }
}