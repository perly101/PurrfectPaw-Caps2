<x-app-layout>
    <div class="flex min-h-screen bg-gray-100">
        {{-- Sidebar for desktop --}}
        <div class="hidden md:block">
            @include('admin.components.sidebar')
        </div>

        {{-- Mobile Navigation --}}
        <div class="md:hidden">
            @include('admin.components.mobile-nav')
        </div>

        {{-- Main Content --}}
        <div class="flex-1 p-4 sm:p-6 md:ml-64">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 sm:mb-6">
                <div>
                    <h2 class="text-xl sm:text-2xl font-semibold text-gray-800">Application Settings</h2>
                    <p class="text-gray-500 text-xs sm:text-sm mt-1">Configure your application settings</p>
                </div>
            </div>
    
    <!-- Session Message -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 sm:p-4 mb-4 sm:mb-6 text-sm sm:text-base" role="alert">
        {{ session('success') }}
    </div>
    @endif
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-3 sm:px-6 py-3 sm:py-4 bg-gray-50 border-b">
            <h3 class="text-base sm:text-lg font-semibold text-gray-700">Settings Management</h3>
        </div>
        
        <!-- Tabs -->
        <div class="border-b border-gray-200 overflow-x-auto">
            <nav class="-mb-px flex flex-nowrap">
                <a id="tab-general" href="#general" class="tab-link tab-active py-3 sm:py-4 px-3 sm:px-6 border-b-2 font-medium text-xs sm:text-sm whitespace-nowrap">
                    General Settings
                </a>
                <a id="tab-email" href="#email" class="tab-link py-3 sm:py-4 px-3 sm:px-6 border-b-2 font-medium text-xs sm:text-sm whitespace-nowrap">
                    Email Configuration
                </a>
                <a id="tab-appearance" href="#appearance" class="tab-link py-3 sm:py-4 px-3 sm:px-6 border-b-2 font-medium text-xs sm:text-sm whitespace-nowrap">
                    Appearance
                </a>
                <a id="tab-security" href="#security" class="tab-link py-3 sm:py-4 px-3 sm:px-6 border-b-2 font-medium text-xs sm:text-sm whitespace-nowrap">
                    Security
                </a>
                <a id="tab-admin" href="{{ route('admin.settings') }}" class="tab-link py-3 sm:py-4 px-3 sm:px-6 border-b-2 font-medium text-xs sm:text-sm whitespace-nowrap">
                    Admin Account
                </a>
            </nav>
        </div>
        
        <!-- Tab Content -->
        <div class="p-3 sm:p-6">
            <!-- General Settings -->
            <div id="general" class="tab-content block">
                <form action="{{ route('admin.settings.general') }}" method="POST" class="space-y-4 sm:space-y-6">
                    @csrf
                    <div>
                        <label for="site_name" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Site Name</label>
                        <input type="text" name="site_name" id="site_name" value="{{ $general['site_name'] ?? '' }}" 
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label for="site_description" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Site Description</label>
                        <textarea name="site_description" id="site_description" rows="3" 
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-sm border-gray-300 rounded-md">{{ $general['site_description'] ?? '' }}</textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="contact_email" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                            <input type="email" name="contact_email" id="contact_email" value="{{ $general['contact_email'] ?? '' }}" 
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-sm border-gray-300 rounded-md">
                        </div>
                        
                        <div>
                            <label for="contact_phone" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Contact Phone</label>
                            <input type="text" name="contact_phone" id="contact_phone" value="{{ $general['contact_phone'] ?? '' }}" 
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                    
                    <div>
                        <label for="address" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Address</label>
                        <textarea name="address" id="address" rows="2" 
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-sm border-gray-300 rounded-md">{{ $general['address'] ?? '' }}</textarea>
                    </div>
                    
                    <div class="mt-4 sm:mt-6">
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-xs sm:text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Save General Settings
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Email Settings -->
            <div id="email" class="tab-content hidden">
                <form action="{{ route('admin.settings.email') }}" method="POST" class="space-y-4 sm:space-y-6">
                    @csrf
                    <div>
                        <label for="mail_driver" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Mail Driver</label>
                        <select name="mail_driver" id="mail_driver" 
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-sm border-gray-300 rounded-md">
                            <option value="smtp" {{ ($email['mail_driver'] ?? '') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                            <option value="sendmail" {{ ($email['mail_driver'] ?? '') == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                            <option value="mailgun" {{ ($email['mail_driver'] ?? '') == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                        </select>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="mail_host" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Mail Host</label>
                            <input type="text" name="mail_host" id="mail_host" value="{{ $email['mail_host'] ?? '' }}" 
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-sm border-gray-300 rounded-md">
                        </div>
                        
                        <div>
                            <label for="mail_port" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Mail Port</label>
                            <input type="number" name="mail_port" id="mail_port" value="{{ $email['mail_port'] ?? '' }}" 
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                    
                    <div>
                        <label for="mail_username" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Mail Username</label>
                        <input type="text" name="mail_username" id="mail_username" value="{{ $email['mail_username'] ?? '' }}" 
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label for="mail_password" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Mail Password</label>
                        <input type="password" name="mail_password" id="mail_password" value="" 
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-sm border-gray-300 rounded-md">
                        <p class="mt-1 text-xs sm:text-sm text-gray-500">Leave empty to keep current password</p>
                    </div>
                    
                    <div>
                        <label for="mail_encryption" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Mail Encryption</label>
                        <select name="mail_encryption" id="mail_encryption" 
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-sm border-gray-300 rounded-md">
                            <option value="" {{ ($email['mail_encryption'] ?? '') == '' ? 'selected' : '' }}>None</option>
                            <option value="tls" {{ ($email['mail_encryption'] ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ ($email['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                        </select>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="mail_from_address" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">From Address</label>
                            <input type="email" name="mail_from_address" id="mail_from_address" value="{{ $email['mail_from_address'] ?? '' }}" 
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-sm border-gray-300 rounded-md">
                        </div>
                        
                        <div>
                            <label for="mail_from_name" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">From Name</label>
                            <input type="text" name="mail_from_name" id="mail_from_name" value="{{ $email['mail_from_name'] ?? '' }}" 
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3 sm:space-x-4 mt-4 sm:mt-6">
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-xs sm:text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Save Email Settings
                        </button>
                        
                        <button type="button" id="test-email-btn" class="w-full sm:w-auto inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-xs sm:text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Test Email Configuration
                        </button>
                    </div>
                </form>
                
                <!-- Email Test Dialog -->
                <div id="test-email-dialog" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 z-50" style="display: none;">
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <div class="bg-white rounded-lg max-w-md w-full p-4 sm:p-6 shadow-xl">
                            <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-3 sm:mb-4">Test Email Configuration</h3>
                            
                            <div class="mb-4">
                                <label for="test_email_address" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">
                                    Send test email to:
                                </label>
                                <input type="email" id="test_email_address" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full text-sm border-gray-300 rounded-md" 
                                    placeholder="Email address for testing">
                            </div>
                            
                            <div id="test-email-result" class="hidden mb-4 p-2 sm:p-3 rounded-md text-sm">
                                <!-- Result will be populated via JavaScript -->
                            </div>
                            
                            <div class="flex flex-col sm:flex-row sm:justify-end gap-2 sm:space-x-3">
                                <button type="button" id="test-email-close" class="w-full sm:w-auto inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-xs sm:text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Close
                                </button>
                                <button type="button" id="test-email-send" class="w-full sm:w-auto inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-xs sm:text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Send Test Email
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Appearance Settings -->
            <div id="appearance" class="tab-content hidden">
                <form action="{{ route('admin.settings.appearance') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Current Logo</label>
                        @if(!empty($appearance['logo'] ?? ''))
                            <img src="{{ asset($appearance['logo']) }}" alt="Site Logo" class="mt-1 h-16">
                        @else
                            <p class="mt-1 text-sm text-gray-500">No logo uploaded</p>
                        @endif
                        <label for="logo" class="block text-sm font-medium text-gray-700 mt-4">Upload New Logo</label>
                        <input type="file" name="logo" id="logo" 
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Current Favicon</label>
                        @if(!empty($appearance['favicon'] ?? ''))
                            <img src="{{ asset($appearance['favicon']) }}" alt="Site Favicon" class="mt-1 h-8">
                        @else
                            <p class="mt-1 text-sm text-gray-500">No favicon uploaded</p>
                        @endif
                        <label for="favicon" class="block text-sm font-medium text-gray-700 mt-4">Upload New Favicon</label>
                        <input type="file" name="favicon" id="favicon" 
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300">
                    </div>
                    
                    <div>
                        <label for="primary_color" class="block text-sm font-medium text-gray-700">Primary Color</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="color" name="primary_color" id="primary_color" value="{{ $appearance['primary_color'] ?? '#4F46E5' }}" 
                                class="h-10 w-14 border-gray-300 rounded-l-md">
                            <input type="text" value="{{ $appearance['primary_color'] ?? '#4F46E5' }}" 
                                class="flex-1 min-w-0 block w-full px-3 py-2 rounded-r-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300"
                                onchange="document.getElementById('primary_color').value = this.value;">
                        </div>
                    </div>
                    
                    <div>
                        <label for="secondary_color" class="block text-sm font-medium text-gray-700">Secondary Color</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="color" name="secondary_color" id="secondary_color" value="{{ $appearance['secondary_color'] ?? '#2563EB' }}" 
                                class="h-10 w-14 border-gray-300 rounded-l-md">
                            <input type="text" value="{{ $appearance['secondary_color'] ?? '#2563EB' }}" 
                                class="flex-1 min-w-0 block w-full px-3 py-2 rounded-r-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300"
                                onchange="document.getElementById('secondary_color').value = this.value;">
                        </div>
                    </div>
                    
                    <div>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Save Appearance Settings
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Security Settings -->
            <div id="security" class="tab-content hidden">
                <form action="{{ route('admin.settings.security') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="flex items-center">
                        <input type="checkbox" name="maintenance_mode" id="maintenance_mode" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                            {{ !empty($security['maintenance_mode'] ?? '') ? 'checked' : '' }}>
                        <label for="maintenance_mode" class="ml-2 block text-sm text-gray-700">
                            Maintenance Mode
                        </label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="enable_registration" id="enable_registration" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                            {{ !empty($security['enable_registration'] ?? '') ? 'checked' : '' }}>
                        <label for="enable_registration" class="ml-2 block text-sm text-gray-700">
                            Enable Public Registration
                        </label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="enable_password_reset" id="enable_password_reset" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                            {{ !empty($security['enable_password_reset'] ?? '') ? 'checked' : '' }}>
                        <label for="enable_password_reset" class="ml-2 block text-sm text-gray-700">
                            Enable Password Reset
                        </label>
                    </div>
                    
                    <div>
                        <label for="max_login_attempts" class="block text-sm font-medium text-gray-700">Max Login Attempts</label>
                        <input type="number" name="max_login_attempts" id="max_login_attempts" value="{{ $security['max_login_attempts'] ?? 5 }}" min="3" max="10" 
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label for="session_lifetime" class="block text-sm font-medium text-gray-700">Session Lifetime (minutes)</label>
                        <input type="number" name="session_lifetime" id="session_lifetime" value="{{ $security['session_lifetime'] ?? 120 }}" min="10" max="1440" 
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Save Security Settings
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Admin Account Settings -->
            <div id="admin" class="tab-content hidden">
                <form action="{{ route('admin.updateSettings') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                            <input type="text" name="first_name" id="first_name" value="{{ $admin->first_name }}" required 
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        
                        <div>
                            <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name</label>
                            <input type="text" name="middle_name" id="middle_name" value="{{ $admin->middle_name }}" 
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                            <input type="text" name="last_name" id="last_name" value="{{ $admin->last_name }}" required 
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ $admin->email }}" required 
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="text" name="phone_number" id="phone_number" value="{{ $admin->phone_number }}" 
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                            <select name="gender" id="gender" 
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <option value="">Select Gender</option>
                                <option value="male" {{ $admin->gender == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ $admin->gender == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="prefer_not_say" {{ $admin->gender == 'prefer_not_say' ? 'selected' : '' }}>Prefer not to say</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="birthday" class="block text-sm font-medium text-gray-700">Birthday</label>
                            <input type="date" name="birthday" id="birthday" value="{{ $admin->birthday }}" 
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" name="password" id="password" 
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <p class="mt-1 text-sm text-gray-500">Leave empty to keep current password</p>
                    </div>
                    
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Update Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabLinks = document.querySelectorAll('.tab-link');
            const tabContents = document.querySelectorAll('.tab-content');
            
            // Handle tab switching
            tabLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Check if this is the Admin Account tab
                    if (this.id === 'tab-admin') {
                        // Allow default navigation for the Admin Account tab
                        return;
                    }
                    
                    // For all other tabs, prevent default navigation
                    e.preventDefault();
                    
                    // Remove active class from all tabs
                    tabLinks.forEach(tab => {
                        tab.classList.remove('tab-active');
                        tab.classList.remove('border-indigo-500');
                        tab.classList.remove('text-indigo-600');
                        tab.classList.add('border-transparent');
                        tab.classList.add('text-gray-500');
                        tab.classList.add('hover:text-gray-700');
                        tab.classList.add('hover:border-gray-300');
                    });
                    
                    // Hide all tab contents
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                        content.classList.remove('block');
                    });
                    
                    // Add active class to current tab
                    this.classList.add('tab-active');
                    this.classList.add('border-indigo-500');
                    this.classList.add('text-indigo-600');
                    this.classList.remove('border-transparent');
                    this.classList.remove('text-gray-500');
                    this.classList.remove('hover:text-gray-700');
                    this.classList.remove('hover:border-gray-300');
                    
                    // Show current tab content
                    const tabId = this.getAttribute('href').substring(1);
                    document.getElementById(tabId).classList.remove('hidden');
                    document.getElementById(tabId).classList.add('block');
                    
                    // Save active tab to localStorage, but not for admin tab
                    localStorage.setItem('activeSettingsTab', tabId);
                });
            });
            
            // Restore active tab from localStorage
            const activeTab = localStorage.getItem('activeSettingsTab');
            if (activeTab) {
                const tabElement = document.querySelector(`a[href="#${activeTab}"]`);
                // Only auto-click the tab if it exists and isn't the admin tab
                if (tabElement && tabElement.id !== 'tab-admin') {
                    tabElement.click();
                }
            }
            
            // Email Test Functionality
            const testEmailBtn = document.getElementById('test-email-btn');
            const testEmailDialog = document.getElementById('test-email-dialog');
            const testEmailClose = document.getElementById('test-email-close');
            const testEmailSend = document.getElementById('test-email-send');
            const testEmailResult = document.getElementById('test-email-result');
            const testEmailAddress = document.getElementById('test_email_address');
            
            // Show the test email dialog
            testEmailBtn?.addEventListener('click', function(e) {
                e.preventDefault();
                testEmailDialog.style.display = 'block';
                // Pre-fill with the contact email if available
                const contactEmailInput = document.querySelector('input[name="contact_email"]');
                if (contactEmailInput && contactEmailInput.value) {
                    testEmailAddress.value = contactEmailInput.value;
                }
            });
            
            // Close the dialog
            testEmailClose?.addEventListener('click', function() {
                testEmailDialog.style.display = 'none';
                // Clear result
                testEmailResult.classList.add('hidden');
            });
            
            // Send test email
            testEmailSend?.addEventListener('click', async function() {
                const email = testEmailAddress.value.trim();
                if (!email) {
                    testEmailResult.innerHTML = 'Please enter a valid email address.';
                    testEmailResult.classList.remove('hidden', 'bg-green-100', 'text-green-800');
                    testEmailResult.classList.add('bg-red-100', 'text-red-800');
                    return;
                }
                
                // Show loading state
                testEmailSend.disabled = true;
                testEmailSend.innerHTML = 'Sending...';
                testEmailResult.innerHTML = 'Sending test email...';
                testEmailResult.classList.remove('hidden', 'bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800');
                testEmailResult.classList.add('bg-blue-100', 'text-blue-800');
                
                try {
                    const response = await fetch(`/admin/test-email?email=${encodeURIComponent(email)}`);
                    const result = await response.json();
                    
                    if (result.test_result && result.test_result.success) {
                        testEmailResult.innerHTML = '✓ Test email sent successfully! Please check your inbox.';
                        testEmailResult.classList.remove('bg-red-100', 'text-red-800', 'bg-blue-100', 'text-blue-800');
                        testEmailResult.classList.add('bg-green-100', 'text-green-800');
                    } else {
                        testEmailResult.innerHTML = '✗ Failed to send test email. Please check your email settings.';
                        testEmailResult.classList.remove('bg-green-100', 'text-green-800', 'bg-blue-100', 'text-blue-800');
                        testEmailResult.classList.add('bg-red-100', 'text-red-800');
                    }
                } catch (error) {
                    testEmailResult.innerHTML = `✗ Error: ${error.message}`;
                    testEmailResult.classList.remove('bg-green-100', 'text-green-800', 'bg-blue-100', 'text-blue-800');
                    testEmailResult.classList.add('bg-red-100', 'text-red-800');
                }
                
                // Reset button state
                testEmailSend.disabled = false;
                testEmailSend.innerHTML = 'Send Test Email';
            });
        });
    </script>
</x-app-layout>
