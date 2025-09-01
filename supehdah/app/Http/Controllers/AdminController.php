<?php

namespace App\Http\Controllers;

require_once base_path('/resources/libs/dompdf/autoload.inc.php');
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\ClinicInfo;
use App\Exports\UsersExport;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Storage;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\DB;
use Exception;

class AdminController extends Controller
{
    // Admin dashboard
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    // Show users based on category (admin, users, clinic)
    public function usermag(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category', 'users');
        
        $query = User::query();
        
        // Apply search filter if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filter by role based on category
        if ($category === 'clinic') {
            $query->where('role', 'clinic');
        } elseif ($category === 'admin') {
            $query->where('role', 'admin');
        } elseif ($category === 'users') {
            $query->where('role', 'user');
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        $users->appends($request->all()); // For pagination with search/filter parameters
        
        return view('admin.usermag', compact('users'));
    }

    // Show settings form
    public function settings()
    {
        $admin = Auth::user(); // Get currently logged-in admin
        return view('admin.settings', compact('admin'));
    }

    // Update admin account details
    public function updateSettings(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'phone_number' => 'nullable|string|max:20',
            'gender' => 'nullable|in:female,male,prefer_not_say',
            'birthday' => 'nullable|date',
            'password' => 'nullable|min:6|confirmed', // password_confirmation field required
        ]);

        $admin = Auth::user();
        $admin->first_name = $request->first_name;
        $admin->middle_name = $request->middle_name;
        $admin->last_name = $request->last_name;
        $admin->email = $request->email;
        $admin->phone_number = $request->phone_number;
        $admin->gender = $request->gender;
        $admin->birthday = $request->birthday;

        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }

        $admin->save();

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
    // Show all registered clinics with search functionality
public function clinicList(Request $request)
{
    $query = ClinicInfo::with('user'); // eager load related user info
    
    // Handle search if provided
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('clinic_name', 'like', "%{$search}%")
              ->orWhere('address', 'like', "%{$search}%")
              ->orWhere('contact_number', 'like', "%{$search}%");
        });
    }
    
    // Paginate results
    $clinics = $query->orderBy('clinic_name')->paginate(10);
    
    return view('admin.clinics.list', compact('clinics'));
}

// View a specific clinic by ID
public function viewClinic($id)
{
    $clinic = ClinicInfo::with('user')->findOrFail($id);
    return view('admin.clinics.view', compact('clinic'));
}

/**
 * Delete a clinic and its associated user account
 */
public function deleteClinic($id)
{
    try {
        // Start a transaction to ensure both operations succeed or fail together
        DB::beginTransaction();
        
        // Find the clinic
        $clinic = ClinicInfo::findOrFail($id);
        
        // Get the associated user account
        $userId = $clinic->user_id;
        
        // Delete the clinic
        $clinic->delete();
        
        // Delete the associated user account
        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                $user->delete();
            }
        }
        
        // Commit the transaction
        DB::commit();
        
        return redirect()->route('admin.clinics')->with('success', 'Clinic and associated user account have been deleted successfully.');
    } catch (Exception $e) {
        // Roll back the transaction on error
        DB::rollBack();
        
        return redirect()->route('admin.clinics')->with('error', 'Failed to delete clinic: ' . $e->getMessage());
    }
}

// public function updateClinicPassword(Request $request, $id)
// {
//     $request->validate([
//         'password' => 'required|string|min:6|confirmed',
//     ]);

//     $user = User::findOrFail($id);

//     if ($user->role !== 'clinic') {
//         return redirect()->back()->withErrors(['error' => 'This user is not a clinic account.']);
//     }

//     $user->password = Hash::make($request->password);
//     $user->save();

//     return redirect()->back()->with('password_updated', 'Clinic account password updated successfully.');
// }

public function updateClinicDetails(Request $request, $id)
{
    $request->validate([
        'clinic_name' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'contact_number' => 'required|string|max:20',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $clinic = ClinicInfo::findOrFail($id);

    $clinic->clinic_name = $request->clinic_name;
    $clinic->address = $request->address;
    $clinic->contact_number = $request->contact_number;


    if ($request->hasFile('profile_picture')) {
        if ($clinic->profile_picture) Storage::delete('public/' . $clinic->profile_picture);
        $clinic->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
    }

    $clinic->save();

    return back()->with('success', 'Clinic details updated successfully.');
}

public function updateClinicAccount(Request $request, $userId)
{
    $user = User::findOrFail($userId);

    if ($user->role !== 'clinic') {
        return redirect()->back()->withErrors(['error' => 'This user is not a clinic account.']);
    }

    $request->validate([
        'first_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,' . $userId,
        'phone_number' => 'nullable|string|max:20',
        'gender' => 'nullable|in:female,male,prefer_not_say',
        'birthday' => 'nullable|date',
        'password' => 'nullable|min:6|confirmed',
    ]);

    $user->first_name = $request->first_name;
    $user->middle_name = $request->middle_name;
    $user->last_name = $request->last_name;
    $user->email = $request->email;
    $user->phone_number = $request->phone_number;
    $user->gender = $request->gender;
    $user->birthday = $request->birthday;

    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    $user->save();

    return back()->with('success', 'Clinic account updated successfully.');
}
public function downloadClinicInfo($id, Request $request)
{
    $clinic = ClinicInfo::with('user')->findOrFail($id);
    
    // Get the password from the request
    $password = $request->query('password');
    
    // Store the password for displaying in the PDF
    if ($clinic->user && $password) {
        $clinic->user->plain_password = $password;
        
        // Optionally, update the user's password in the database if needed
        // Uncomment these lines if you want to update the password
        // $clinic->user->password = Hash::make($password);
        // $clinic->user->save();
    }

    // Render a Blade view to HTML
    $html = view('admin.clinics.pdf', compact('clinic'))->render();

    // DOMPDF setup
    $options = new Options();
    $options->set('defaultFont', 'Arial');

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Output the PDF
    return response($dompdf->output(), 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="clinic_partnership_agreement.pdf"');
}

public function refreshStats()
{
    return response()->json([
        'userCount' => User::where('role', 'user')->count(),
        'clinicCount' => User::where('role', 'clinic')->count(),
        'todayCount' => User::whereDate('created_at', today())->count(),
    ]);
}

public function getUserStats($type)
{
    if ($type === 'month') {
        $userStats = User::where('role', 'user')
            ->selectRaw('MONTH(created_at) as label, COUNT(*) as total')
            ->groupBy('label')
            ->orderBy('label')
            ->pluck('total', 'label');

        $clinicStats = User::where('role', 'clinic')
            ->selectRaw('MONTH(created_at) as label, COUNT(*) as total')
            ->groupBy('label')
            ->orderBy('label')
            ->pluck('total', 'label');

        $labels = collect(range(1, 12))->map(function ($m) {
            return date("F", mktime(0, 0, 0, $m, 1));
        });
    } else { // week
        $userStats = User::where('role', 'user')
            ->selectRaw('WEEK(created_at) as label, COUNT(*) as total')
            ->groupBy('label')
            ->orderBy('label')
            ->pluck('total', 'label');

        $clinicStats = User::where('role', 'clinic')
            ->selectRaw('WEEK(created_at) as label, COUNT(*) as total')
            ->groupBy('label')
            ->orderBy('label')
            ->pluck('total', 'label');

        $maxWeek = now()->endOfYear()->weekOfYear;
        $labels = collect(range(1, $maxWeek))->map(fn($w) => "Week $w");
    }

    // Normalize data (fill missing months/weeks with 0)
    $usersData = [];
    $clinicsData = [];
    foreach ($labels as $i => $label) {
        $usersData[] = $userStats->get($i + 1, 0);
        $clinicsData[] = $clinicStats->get($i + 1, 0);
    }

    return response()->json([
        'labels' => $labels,
        'users' => $usersData,
        'clinics' => $clinicsData,
    ]);
}

// Delete a user
public function deleteUser($id)
{
    $user = User::findOrFail($id);

    if ($user->role === 'admin') {
        return redirect()->back()->withErrors(['error' => 'Cannot delete an admin account.']);
    }

    $user->delete();
    return redirect()->route('admin.usermag')->with('success', 'User deleted successfully.');
}

// Show edit form
public function editUser($id)
{
    $user = User::findOrFail($id);
    return view('admin.users.edit', compact('user'));
}

// Update user
public function updateUser(Request $request, $id)
{
    $user = User::findOrFail($id);

    $request->validate([
        'first_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,' . $id,
        'phone_number' => 'nullable|string|max:20',
        'gender' => 'nullable|in:female,male,prefer_not_say',
        'birthday' => 'nullable|date',
        'password' => 'nullable|min:6|confirmed',
        'role' => 'required|in:user,admin,clinic',
    ]);

    $user->first_name = $request->first_name;
    $user->middle_name = $request->middle_name;
    $user->last_name = $request->last_name;
    $user->email = $request->email;
    $user->phone_number = $request->phone_number;
    $user->gender = $request->gender;
    $user->birthday = $request->birthday;
    $user->role = $request->role;

    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    $user->save();

    return redirect()->route('admin.usermag')->with('success', 'User updated successfully.');
}

/**
 * Handle bulk actions on users
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\RedirectResponse
 */
public function bulkAction(Request $request)
{
    $selectedUsers = $request->input('selected_users', []);
    $action = $request->input('action');
    
    if (empty($selectedUsers)) {
        return redirect()->back()->with('error', 'No users were selected.');
    }
    
    switch ($action) {
        case 'delete':
            // Don't allow deleting your own account
            if (in_array(auth()->id(), $selectedUsers)) {
                $selectedUsers = array_diff($selectedUsers, [auth()->id()]);
                $message = "You can't delete your own account. Other selected users were deleted.";
            } else {
                $message = 'Selected users were successfully deleted.';
            }
            
            // Delete users
            User::whereIn('id', $selectedUsers)->delete();
            return redirect()->back()->with('success', $message);
            
        case 'change_role':
            $newRole = $request->input('new_role');
            
            if (!in_array($newRole, ['user', 'admin', 'clinic'])) {
                return redirect()->back()->with('error', 'Invalid role selected.');
            }
            
            // Don't allow changing your own role
            if (in_array(auth()->id(), $selectedUsers)) {
                $selectedUsers = array_diff($selectedUsers, [auth()->id()]);
                $message = "You can't change your own role. Other selected users' roles were updated.";
            } else {
                $message = 'Selected users were updated to role: ' . ucfirst($newRole);
            }
            
            // Update roles
            User::whereIn('id', $selectedUsers)->update(['role' => $newRole]);
            return redirect()->back()->with('success', $message);
            
        default:
            return redirect()->back()->with('error', 'Invalid action specified.');
    }
}

/**
 * Export users data as CSV
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Symfony\Component\HttpFoundation\StreamedResponse
 */
public function exportUsers(Request $request)
{
    $format = $request->input('format', 'csv');
    $category = $request->input('category', 'all');
    
    // Build query based on filters
    $query = User::query();
    
    if ($category === 'users') {
        $query->where('role', 'user');
    } elseif ($category === 'admin') {
        $query->where('role', 'admin');
    } elseif ($category === 'clinic') {
        $query->where('role', 'clinic');
    }
    
    // Get filtered users
    $users = $query->get();
    
    // Create the exporter instance
    $exporter = new UsersExport($users);
    
    // Handle CSV export
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="users-' . date('Y-m-d') . '.csv"',
    ];
    
    $callback = function() use ($exporter) {
        echo $exporter->toCsv();
    };
    
    return response()->stream($callback, 200, $headers);
}


}
