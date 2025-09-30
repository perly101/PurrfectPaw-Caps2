<x-app-layout>
    {{-- Include mobil                                <button type="button" class="bg-green-500 hover:bg-green-600 text-white px-3 md:px-4 py-1.5 md:py-2 rounded-lg flex items-center text-xs md:text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5 mr-0 md:mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="hidden md:inline">Export</span>
                                </button>
                                <div id="headerExportDropdown" class="dropdown-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-10 border border-gray-200">ation component --}}
    @include('admin.components.mobile-nav')
    
    <div class="flex flex-col md:flex-row min-h-screen bg-gray-100">
        {{-- Sidebar (hidden on mobile) --}}
        <div class="hidden md:block">
            @include('admin.components.sidebar')
        </div>

        {{-- Main Content --}}
        <div class="flex-1 p-4 md:p-6 md:ml-64 mt-12 md:mt-0">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div class="mb-4 md:mb-0">
                    <h2 class="text-xl md:text-2xl font-semibold text-gray-800">User Management</h2>
                    <p class="text-gray-500 text-sm mt-1">Manage and monitor all system users</p>
                </div>
                
                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                    <form action="{{ route('admin.usermag') }}" method="GET" class="flex flex-1 md:flex-auto">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users..." 
                            class="border border-gray-300 rounded-l-lg px-2 md:px-4 py-2 focus:ring focus:ring-blue-200 focus:border-blue-400 text-sm md:text-base w-full" />
                        <button type="submit" class="bg-blue-500 text-white px-3 md:px-4 py-2 rounded-r-lg hover:bg-blue-600 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>
                    
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.usermag') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded-lg transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </a>
                        
                        <div class="dropdown relative">
                            <button type="button" class="bg-green-500 hover:bg-green-600 text-white px-3 md:px-4 py-1.5 md:py-2 rounded-lg flex items-center text-xs md:text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5 mr-0 md:mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="hidden md:inline">Export</span>
                            </button>
                            <div id="exportDropdown" class="dropdown-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-10 border border-gray-200">
                                <a href="{{ route('admin.users.export', ['format' => 'csv', 'category' => request('category', 'users')]) }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">
                                    Export as CSV
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-4 md:p-6">
                <!-- Category Filter -->
                <div class="mb-4 flex flex-wrap gap-2">
                    <a href="{{ route('admin.usermag', array_merge(request()->except('category'), ['category' => 'users'])) }}"
                       class="px-2 sm:px-3 md:px-4 py-1.5 md:py-2 rounded-lg text-xs sm:text-sm md:text-base font-medium md:font-semibold transition {{ request('category', 'users') == 'users' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                        Users
                    </a>
                    <a href="{{ route('admin.usermag', array_merge(request()->except('category'), ['category' => 'clinic'])) }}"
                       class="px-2 sm:px-3 md:px-4 py-1.5 md:py-2 rounded-lg text-xs sm:text-sm md:text-base font-medium md:font-semibold transition {{ request('category') == 'clinic' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                        Clinic
                    </a>
                    <a href="{{ route('admin.usermag', array_merge(request()->except('category'), ['category' => 'doctor'])) }}"
                       class="px-2 sm:px-3 md:px-4 py-1.5 md:py-2 rounded-lg text-xs sm:text-sm md:text-base font-medium md:font-semibold transition {{ request('category') == 'doctor' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                        Doctor
                    </a>
                    <a href="{{ route('admin.usermag', array_merge(request()->except('category'), ['category' => 'admin'])) }}"
                       class="px-2 sm:px-3 md:px-4 py-1.5 md:py-2 rounded-lg text-xs sm:text-sm md:text-base font-medium md:font-semibold transition {{ request('category') == 'admin' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                        Admin
                    </a>
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

                <!-- Bulk Actions -->
                <div class="mb-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <button id="selectAllBtn" type="button" class="text-xs md:text-sm bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-1.5 md:py-2 px-3 md:px-4 rounded-lg">
                            <span class="hidden sm:inline">Select All</span>
                            <span class="sm:hidden">Select</span>
                        </button>
                        <button id="deselectAllBtn" type="button" class="text-xs md:text-sm bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-1.5 md:py-2 px-3 md:px-4 rounded-lg">
                            <span class="hidden sm:inline">Deselect All</span>
                            <span class="sm:hidden">Deselect</span>
                        </button>
                    </div>
                    
                    <div class="bulk-actions hidden items-center gap-2" id="bulkActionsContainer">
                        <span id="selectedCount" class="text-xs md:text-sm font-medium text-gray-700">0 users selected</span>
                        
                        <div class="dropdown relative">
                            <button type="button" class="bg-blue-500 hover:bg-blue-600 text-white px-3 md:px-4 py-1.5 md:py-2 rounded-lg text-xs md:text-sm">
                                Bulk Actions
                            </button>
                            <div id="bulkActionsDropdown" class="dropdown-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-10 border border-gray-200">
                                <button type="button" id="bulkChangeRole" class="block w-full text-left px-4 py-2 text-gray-800 hover:bg-gray-100">
                                    Change Role
                                </button>
                                <button type="button" id="bulkDelete" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">
                                    Delete Selected
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="bulkActionForm" action="{{ route('admin.users.bulk') }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="action" id="bulkAction" value="">
                    <input type="hidden" name="new_role" id="newRoleValue" value="">
                </form>
                
                <div class="overflow-x-auto responsive-table-container">
                    <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden responsive-table">
                            <thead class="bg-gray-100 text-gray-700 text-xs md:text-sm uppercase tracking-wide">
                                <tr>
                                    <th class="px-2 md:px-4 py-2 md:py-3 w-8">
                                        <div class="flex items-center">
                                            <input type="checkbox" id="selectAll" class="form-checkbox h-3 md:h-4 w-3 md:w-4 text-blue-600 transition duration-150 ease-in-out">
                                        </div>
                                    </th>
                                    <th class="px-2 md:px-6 py-2 md:py-3 text-left hidden sm:table-cell">#</th>
                                    <th class="px-2 md:px-6 py-2 md:py-3 text-left">Full Name</th>
                                    <th class="px-2 md:px-6 py-2 md:py-3 text-left hidden md:table-cell">Email</th>
                                    <th class="px-2 md:px-6 py-2 md:py-3 text-left hidden lg:table-cell">Phone</th>
                                    <th class="px-2 md:px-6 py-2 md:py-3 text-left">Role</th>
                                    @if(request('category') == 'doctor')
                                    <th class="px-2 md:px-6 py-2 md:py-3 text-left hidden lg:table-cell">Clinic</th>
                                    @endif
                                    <th class="px-2 md:px-6 py-2 md:py-3 text-left hidden md:table-cell">Registered</th>
                                    <th class="px-2 md:px-6 py-2 md:py-3 text-center">Actions</th>
                                </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-gray-700 text-xs md:text-sm">
                            @php
                                $category = request('category', 'users');
                            @endphp
                            @forelse ($users as $index => $user)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-2 md:px-4 py-2 md:py-4">
                                        <div class="flex items-center">
                                            <input type="checkbox" name="selected_users[]" value="{{ $user->id }}" class="user-checkbox form-checkbox h-3 md:h-4 w-3 md:w-4 text-blue-600 transition duration-150 ease-in-out">
                                        </div>
                                    </td>
                                    <td class="px-2 md:px-6 py-2 md:py-4 hidden sm:table-cell">{{ $index + 1 }}</td>
                                        <td class="px-2 md:px-6 py-2 md:py-4 font-medium">
                                            <div>{{ $user->first_name }} {{ $user->middle_name ? $user->middle_name . ' ' : '' }}{{ $user->last_name }}</div>
                                            <div class="text-xs text-gray-500 md:hidden">{{ $user->email }}</div>
                                        </td>
                                        <td class="px-2 md:px-6 py-2 md:py-4 hidden md:table-cell">{{ $user->email }}</td>
                                        <td class="px-2 md:px-6 py-2 md:py-4 hidden lg:table-cell">{{ $user->phone_number ?? '—' }}</td>
                                        <td class="px-2 md:px-6 py-2 md:py-4">
                                            @if($user->role == 'admin')
                                                <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">Admin</span>
                                            @elseif($user->role == 'clinic')
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">Clinic Staff</span>
                                            @elseif($user->role == 'doctor')
                                                <span class="px-2 py-1 bg-teal-100 text-teal-800 text-xs font-medium rounded-full">Doctor</span>
                                            @else
                                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">User</span>
                                            @endif
                                        </td>
                                        @if(request('category') == 'doctor')
                                        <td class="px-2 md:px-6 py-2 md:py-4 hidden lg:table-cell">
                                            @php
                                                $doctorProfile = $user->doctorProfile;
                                                $clinic = $doctorProfile ? $doctorProfile->clinic : null;
                                            @endphp
                                            
                                            @if($clinic)
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded">{{ $clinic->clinic_name }}</span>
                                            @elseif($doctorProfile)
                                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded">Has Profile, No Clinic</span>
                                            @else
                                                <span class="text-gray-500 italic">Not assigned</span>
                                            @endif
                                        </td>
                                        @endif
                                        <td class="px-2 md:px-6 py-2 md:py-4 text-gray-500 hidden md:table-cell">{{ $user->created_at->format('M d, Y') }}</td>
                                        <td class="px-2 md:px-6 py-2 md:py-4 flex flex-wrap items-center justify-center gap-2">
                                            <!-- Edit Button -->
                                            <button type="button" onclick="openModal('editModal-{{ $user->id }}')"
                                                class="bg-blue-500 hover:bg-blue-600 text-white text-xs md:text-sm px-2 md:px-3 py-1 md:py-2 rounded-lg shadow">
                                                <span class="hidden sm:inline">Edit</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:hidden" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                </svg>
                                            </button>

                                            <!-- Delete Form -->
                                            <form action="{{ route('admin.users.delete', $user->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="bg-red-600 hover:bg-red-500 text-white text-xs md:text-sm px-2 md:px-3 py-1 md:py-2 rounded-lg shadow-md transition">
                                                    <span class="hidden sm:inline">Delete</span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:hidden" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Edit Modal -->
                                    <div id="editModal-{{ $user->id }}" 
                                        class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 p-3 md:p-0 overflow-y-auto">

                                        <!-- Draggable Modal -->
                                        <div id="draggable-{{ $user->id }}" 
                                            class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-auto p-4 md:p-6 relative cursor-move"
                                            style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); max-height: 90vh; overflow-y: auto;">

                                            <!-- Close button -->
                                            <button onclick="closeModal('editModal-{{ $user->id }}')"
                                                class="absolute top-2 right-2 md:top-3 md:right-3 text-gray-400 hover:text-gray-600 text-xl md:text-2xl">
                                                &times;
                                            </button>

                                            <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-3 md:mb-4">Edit User</h2>

                                            @if($errors->any())
                                            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                                                <p class="font-bold">Please fix the following errors:</p>
                                                <ul class="list-disc ml-4">
                                                    @foreach($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            @endif

                                            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-4">
                                                @csrf
                                                @method('PUT')

                                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 md:gap-4">
                                                    <div>
                                                        <label class="block text-gray-600 text-sm md:text-base mb-1">First Name</label>
                                                        <input type="text" name="first_name" value="{{ $user->first_name }}"
                                                            class="w-full border border-gray-300 rounded-lg px-2 md:px-3 py-1.5 md:py-2 text-sm md:text-base focus:ring focus:ring-blue-200">
                                                    </div>
                                                    <div>
                                                        <label class="block text-gray-600 text-sm md:text-base mb-1">Middle Name</label>
                                                        <input type="text" name="middle_name" value="{{ $user->middle_name }}"
                                                            class="w-full border border-gray-300 rounded-lg px-2 md:px-3 py-1.5 md:py-2 text-sm md:text-base focus:ring focus:ring-blue-200">
                                                    </div>
                                                    <div>
                                                        <label class="block text-gray-600 text-sm md:text-base mb-1">Last Name</label>
                                                        <input type="text" name="last_name" value="{{ $user->last_name }}"
                                                            class="w-full border border-gray-300 rounded-lg px-2 md:px-3 py-1.5 md:py-2 text-sm md:text-base focus:ring focus:ring-blue-200">
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
                                                    <div>
                                                        <label class="block text-gray-600 text-sm md:text-base mb-1">Email</label>
                                                        <input type="email" name="email" value="{{ $user->email }}"
                                                            class="w-full border border-gray-300 rounded-lg px-2 md:px-3 py-1.5 md:py-2 text-sm md:text-base focus:ring focus:ring-blue-200">
                                                    </div>
                                                    <div>
                                                        <label class="block text-gray-600 text-sm md:text-base mb-1">Phone Number</label>
                                                        <input type="text" name="phone_number" value="{{ $user->phone_number }}"
                                                            class="w-full border border-gray-300 rounded-lg px-2 md:px-3 py-1.5 md:py-2 text-sm md:text-base focus:ring focus:ring-blue-200">
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
                                                    <div>
                                                        <label class="block text-gray-600 text-sm md:text-base mb-1">Birthday</label>
                                                        <input type="date" name="birthday" value="{{ $user->birthday }}"
                                                            class="w-full border border-gray-300 rounded-lg px-2 md:px-3 py-1.5 md:py-2 text-sm md:text-base focus:ring focus:ring-blue-200">
                                                    </div>
                                                    <div>
                                                        <label class="block text-gray-600 text-sm md:text-base mb-1">Gender</label>
                                                        <select name="gender" class="w-full border border-gray-300 rounded-lg px-2 md:px-3 py-1.5 md:py-2 text-sm md:text-base focus:ring focus:ring-blue-200">
                                                            <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Male</option>
                                                            <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Female</option>
                                                            <option value="prefer_not_say" {{ $user->gender == 'prefer_not_say' ? 'selected' : '' }}>Prefer not to say</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-gray-600 text-sm md:text-base mb-1">Role</label>
                                                    <select name="role" class="w-full border border-gray-300 rounded-lg px-2 md:px-3 py-1.5 md:py-2 text-sm md:text-base focus:ring focus:ring-blue-200">
                                                        <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                                        <option value="clinic" {{ $user->role == 'clinic' ? 'selected' : '' }}>Clinic Staff</option>
                                                        <option value="doctor" {{ $user->role == 'doctor' ? 'selected' : '' }}>Doctor</option>
                                                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="block text-gray-600 text-sm md:text-base mb-1">Password (leave blank if unchanged)</label>
                                                    <input type="password" name="password"
                                                        class="w-full border border-gray-300 rounded-lg px-2 md:px-3 py-1.5 md:py-2 text-sm md:text-base focus:ring focus:ring-blue-200">
                                                    <input type="password" name="password_confirmation" placeholder="Confirm password"
                                                        class="w-full border border-gray-300 rounded-lg px-2 md:px-3 py-1.5 md:py-2 mt-2 text-sm md:text-base focus:ring focus:ring-blue-200">
                                                </div>

                                                <div class="flex justify-end gap-2">
                                                    <button type="button" onclick="closeModal('editModal-{{ $user->id }}')"
                                                        class="bg-gray-500 hover:bg-gray-600 text-white px-3 md:px-4 py-1.5 md:py-2 rounded-lg shadow text-xs md:text-sm">
                                                        Cancel
                                                    </button>
                                                    <button type="submit"
                                                        class="bg-green-500 hover:bg-green-600 text-white px-3 md:px-4 py-1.5 md:py-2 rounded-lg shadow text-xs md:text-sm">
                                                        Update
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                            @empty
                                <tr>
                                    <td colspan="{{ request('category') == 'doctor' ? 8 : 7 }}" class="px-6 py-4 text-center text-gray-500">
                                        @if(request('search'))
                                            @if($category == 'clinic')
                                                No clinic accounts found matching "{{ request('search') }}".
                                            @elseif($category == 'admin')
                                                No admin accounts found matching "{{ request('search') }}".
                                            @elseif($category == 'doctor')
                                                No doctor accounts found matching "{{ request('search') }}".
                                            @else
                                                No user accounts found matching "{{ request('search') }}".
                                            @endif
                                        @else
                                            @if($category == 'clinic')
                                                No clinic accounts found.
                                            @elseif($category == 'admin')
                                                No admin accounts found.
                                            @elseif($category == 'doctor')
                                                No doctor accounts found.
                                            @else
                                                No user accounts found.
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    @if(is_object($users) && method_exists($users, 'links'))
                        {{ $users->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Script --}}
    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        // Make modals draggable on desktop, but not on mobile
        document.querySelectorAll("[id^='draggable-']").forEach(modal => {
            let isDragging = false;
            let offsetX, offsetY;
            
            // Only enable dragging on larger screens
            const isMobile = window.matchMedia("(max-width: 768px)").matches;
            
            if (!isMobile) {
                modal.addEventListener("mousedown", (e) => {
                    // Only drag if clicked outside inputs/forms/buttons
                    if (e.target.closest("input, button, form, textarea, label, select")) return;
    
                    isDragging = true;
                    offsetX = e.clientX - modal.offsetLeft;
                    offsetY = e.clientY - modal.offsetTop;
                    modal.style.transition = "none"; // prevent snapping glitch
                });
    
                document.addEventListener("mousemove", (e) => {
                    if (isDragging) {
                        modal.style.left = (e.clientX - offsetX) + "px";
                        modal.style.top = (e.clientY - offsetY) + "px";
                        modal.style.transform = "none"; // cancel center transform once moved
                    }
                });
    
                document.addEventListener("mouseup", () => {
                    isDragging = false;
                });
            } else {
                // On mobile, ensure modal is centered and scrollable
                modal.style.position = "relative";
                modal.style.top = "auto";
                modal.style.left = "auto";
                modal.style.transform = "none";
                modal.style.margin = "1rem auto";
                modal.style.maxHeight = "80vh";
                modal.style.overflowY = "auto";
                modal.classList.remove("cursor-move");
            }
        });

        // Bulk Actions and Export Functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Export dropdown
            const exportBtn = document.querySelector('.dropdown button');
            const exportDropdown = document.getElementById('headerExportDropdown');
            
            exportBtn.addEventListener('click', function() {
                exportDropdown.classList.toggle('hidden');
            });
            
            // Select All checkbox functionality
            const selectAllCheckbox = document.getElementById('selectAll');
            const userCheckboxes = document.querySelectorAll('.user-checkbox');
            const bulkActionsContainer = document.getElementById('bulkActionsContainer');
            const selectedCountElement = document.getElementById('selectedCount');
            
            // Select All button
            document.getElementById('selectAllBtn').addEventListener('click', function() {
                userCheckboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
                updateSelectedCount();
            });
            
            // Deselect All button
            document.getElementById('deselectAllBtn').addEventListener('click', function() {
                userCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                updateSelectedCount();
            });
            
            // Select All checkbox
            selectAllCheckbox.addEventListener('change', function() {
                userCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
                updateSelectedCount();
            });
            
            // Individual checkbox change
            userCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedCount);
            });
            
            // Update selected count
            function updateSelectedCount() {
                const selectedCount = document.querySelectorAll('.user-checkbox:checked').length;
                selectedCountElement.textContent = selectedCount + (selectedCount === 1 ? ' user selected' : ' users selected');
                
                if (selectedCount > 0) {
                    bulkActionsContainer.classList.remove('hidden');
                    bulkActionsContainer.classList.add('flex');
                } else {
                    bulkActionsContainer.classList.add('hidden');
                    bulkActionsContainer.classList.remove('flex');
                }
            }
            
            // Bulk Actions dropdown
            const bulkActionsBtn = document.querySelector('.bulk-actions button');
            const bulkActionsDropdown = document.getElementById('bulkActionsDropdown');
            
            bulkActionsBtn.addEventListener('click', function() {
                bulkActionsDropdown.classList.toggle('hidden');
            });
            
            // Bulk Delete action
            document.getElementById('bulkDelete').addEventListener('click', function() {
                const selectedCount = document.querySelectorAll('.user-checkbox:checked').length;
                
                if (selectedCount === 0) {
                    alert('No users selected.');
                    return;
                }
                
                if (confirm(`Are you sure you want to delete ${selectedCount} user(s)?`)) {
                    // Collect all selected user IDs
                    const selectedUserIds = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(checkbox => checkbox.value);
                    
                    // Add them to the form
                    const form = document.getElementById('bulkActionForm');
                    
                    // Clear any existing hidden fields for user IDs
                    form.querySelectorAll('input[name="selected_users[]"]').forEach(input => input.remove());
                    
                    // Add new hidden fields for selected users
                    selectedUserIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'selected_users[]';
                        input.value = id;
                        form.appendChild(input);
                    });
                    
                    document.getElementById('bulkAction').value = 'delete';
                    document.getElementById('bulkActionForm').submit();
                }
            });
            
            // Bulk Change Role action
            document.getElementById('bulkChangeRole').addEventListener('click', function() {
                const selectedCount = document.querySelectorAll('.user-checkbox:checked').length;
                
                if (selectedCount === 0) {
                    alert('No users selected.');
                    return;
                }
                
                const newRole = prompt('Change selected users to role (user, clinic, doctor, admin):');
                
                if (newRole && ['user', 'clinic', 'doctor', 'admin'].includes(newRole)) {
                    // Collect all selected user IDs
                    const selectedUserIds = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(checkbox => checkbox.value);
                    
                    // Add them to the form
                    const form = document.getElementById('bulkActionForm');
                    
                    // Clear any existing hidden fields for user IDs
                    form.querySelectorAll('input[name="selected_users[]"]').forEach(input => input.remove());
                    
                    // Add new hidden fields for selected users
                    selectedUserIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'selected_users[]';
                        input.value = id;
                        form.appendChild(input);
                    });
                    
                    document.getElementById('bulkAction').value = 'change_role';
                    document.getElementById('newRoleValue').value = newRole;
                    document.getElementById('bulkActionForm').submit();
                } else if (newRole) {
                    alert('Invalid role. Please use user, clinic, doctor, or admin.');
                }
            });
            
            // Close dropdowns when clicking outside
            document.addEventListener('click', function(event) {
                if (!event.target.closest('.dropdown')) {
                    document.querySelectorAll('.dropdown-menu').forEach(menu => {
                        menu.classList.add('hidden');
                    });
                }
            });
            
            // Initialize selected count
            updateSelectedCount();
        });
    </script>

    {{-- SweetAlert for Clinic Registration Success --}}
    @if(session('registration_success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: "{{ session('registration_success')['title'] }}",
                confirmButtonColor: '#4F46E5'
            });
        });
    </script>
    @endif

    {{-- SweetAlert for User Success and Errors --}}
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#4F46E5'
            });
        });
    </script>
    @endif
    
    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#4F46E5'
            });
        });
    </script>
    @endif

    {{-- Modal handling improvements --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Better modal handling
            window.openModal = function(modalId) {
                const modal = document.getElementById(modalId);
                modal.classList.remove('hidden');
                const draggableElement = modal.querySelector('[id^="draggable-"]');
                
                // Reset position when opening modal
                if (draggableElement) {
                    draggableElement.style.top = "50%";
                    draggableElement.style.left = "50%";
                    draggableElement.style.transform = "translate(-50%, -50%)";
                    draggableElement.removeAttribute('data-dragged');
                }
                
                makeDraggable(modalId);
            }
            
            // Mark modal as dragged when user interacts with it
            document.querySelectorAll('[id^="draggable-"]').forEach(element => {
                element.addEventListener('mousedown', function() {
                    this.setAttribute('data-dragged', 'true');
                });
            });
        });
    </script>
</x-app-layout>
