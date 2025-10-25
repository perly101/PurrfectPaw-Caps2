<x-app-layout>
    <div class="flex min-h-screen bg-gray-100">
        {{-- Sidebar (direct include) --}}
        @include('admin.components.sidebar')

        {{-- Main Content --}}
        <div class="flex-1 p-6 ml-64">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">Application Settings</h2>
                        <p class="text-gray-500 text-sm mt-1">Configure global system settings and preferences</p>
                    </div>
                    
                    <div>
                        <button type="button" id="saveAllSettings" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition">
                            Save All Changes
                        </button>
                    </div>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Settings Tabs -->
                <div class="mb-6 border-b border-gray-200">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                        <li class="mr-2">
                            <a href="#general" class="inline-block p-4 border-b-2 border-blue-600 text-blue-600 active" 
                               onclick="showTab('general')">
                                General Settings
                            </a>
                        </li>
                        <li class="mr-2">
                            <a href="#appearance" class="inline-block p-4 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300"
                               onclick="showTab('appearance')">
                                Appearance
                            </a>
                        </li>
                        <li class="mr-2">
                            <a href="#email" class="inline-block p-4 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300"
                               onclick="showTab('email')">
                                Email Configuration
                            </a>
                        </li>
                        <li class="mr-2">
                            <a href="#security" class="inline-block p-4 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300"
                               onclick="showTab('security')">
                                Security
                            </a>
                        </li>
                        <li class="mr-2">
                            <a href="#advanced" class="inline-block p-4 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300"
                               onclick="showTab('advanced')">
                                Advanced
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- General Settings Tab -->
                <div id="general" class="setting-tab">
                    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Application Name</label>
                                <input type="text" name="app_name" value="{{ $settings->app_name ?? config('app.name') }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                                <p class="mt-1 text-xs text-gray-500">This name will be displayed throughout the application.</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                                <input type="email" name="contact_email" value="{{ $settings->contact_email ?? '' }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                                <p class="mt-1 text-xs text-gray-500">Used for system notifications and user contact.</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <input type="text" name="phone_number" value="{{ $settings->phone_number ?? '' }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <input type="text" name="address" value="{{ $settings->address ?? '' }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Default User Role</label>
                            <select name="default_role" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                                <option value="user" {{ ($settings->default_role ?? 'user') == 'user' ? 'selected' : '' }}>User</option>
                                <option value="clinic" {{ ($settings->default_role ?? '') == 'clinic' ? 'selected' : '' }}>Clinic</option>
                                <option value="admin" {{ ($settings->default_role ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Role assigned to new registrations (usually should be 'User').</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Timezone</label>
                            <select name="timezone" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                                <option value="UTC" {{ ($settings->timezone ?? config('app.timezone')) == 'UTC' ? 'selected' : '' }}>UTC</option>
                                <option value="Asia/Manila" {{ ($settings->timezone ?? config('app.timezone')) == 'Asia/Manila' ? 'selected' : '' }}>Asia/Manila (PHT)</option>
                                <!-- Add more timezone options as needed -->
                            </select>
                        </div>
                        
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition">
                            Save General Settings
                        </button>
                    </form>
                </div>

                <!-- Appearance Tab (hidden by default) -->
                <div id="appearance" class="setting-tab hidden">
                    <form action="{{ route('admin.settings.appearance') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Site Logo</label>
                                <div class="flex items-center">
                                    @if($settings->logo ?? false)
                                        <img src="{{ asset('storage/' . $settings->logo) }}" alt="Logo" class="h-12 mr-4">
                                    @endif
                                    <input type="file" name="logo" class="border border-gray-300 rounded-lg px-3 py-2">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Recommended size: 200x60 pixels.</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Favicon</label>
                                <div class="flex items-center">
                                    @if($settings->favicon ?? false)
                                        <img src="{{ asset('storage/' . $settings->favicon) }}" alt="Favicon" class="h-8 mr-4">
                                    @endif
                                    <input type="file" name="favicon" class="border border-gray-300 rounded-lg px-3 py-2">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Recommended size: 32x32 pixels.</p>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Primary Color</label>
                            <div class="flex items-center space-x-2">
                                <input type="color" name="primary_color" value="{{ $settings->primary_color ?? '#3B82F6' }}"
                                    class="rounded-lg border border-gray-300 h-10 w-20">
                                <input type="text" name="primary_color_hex" value="{{ $settings->primary_color ?? '#3B82F6' }}"
                                    class="border border-gray-300 rounded-lg px-3 py-2 w-32">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Secondary Color</label>
                            <div class="flex items-center space-x-2">
                                <input type="color" name="secondary_color" value="{{ $settings->secondary_color ?? '#10B981' }}"
                                    class="rounded-lg border border-gray-300 h-10 w-20">
                                <input type="text" name="secondary_color_hex" value="{{ $settings->secondary_color ?? '#10B981' }}"
                                    class="border border-gray-300 rounded-lg px-3 py-2 w-32">
                            </div>
                        </div>
                        
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition">
                            Save Appearance Settings
                        </button>
                    </form>
                </div>

                <!-- Other tabs would be defined similarly -->
                <div id="email" class="setting-tab hidden">
                    <form class="space-y-6">
                        <!-- Email settings content -->
                        <p class="text-gray-700">Email configuration settings would go here...</p>
                    </form>
                </div>

                <div id="security" class="setting-tab hidden">
                    <form class="space-y-6">
                        <!-- Security settings content -->
                        <p class="text-gray-700">Security settings would go here...</p>
                    </form>
                </div>

                <div id="advanced" class="setting-tab hidden">
                    <form class="space-y-6">
                        <!-- Advanced settings content -->
                        <p class="text-gray-700">Advanced configuration settings would go here...</p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabId) {
            // Hide all tabs
            document.querySelectorAll('.setting-tab').forEach(tab => {
                tab.classList.add('hidden');
            });
            
            // Show the selected tab
            document.getElementById(tabId).classList.remove('hidden');
            
            // Update active tab styling
            document.querySelectorAll('ul a').forEach(link => {
                link.classList.remove('border-blue-600', 'text-blue-600');
                link.classList.add('border-transparent');
            });
            
            // Set active tab
            document.querySelector(`a[href="#${tabId}"]`).classList.add('border-blue-600', 'text-blue-600');
            document.querySelector(`a[href="#${tabId}"]`).classList.remove('border-transparent');
        }
        
        // Handle "Save All Changes" button
        document.getElementById('saveAllSettings').addEventListener('click', function() {
            // You would need to implement this to submit all forms
            alert('This would save all settings from all tabs.');
        });
    </script>
</x-app-layout>
