<x-app-layout>
    <div class="flex min-h-screen bg-gray-100">
        {{-- Sidebar (direct include) --}}
        @include('admin.components.sidebar')

        {{-- Main Content --}}
        <div class="flex-1 p-6 ml-64">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">User Management</h2>
                    
                    <div class="flex items-center space-x-2">
                        <form action="{{ route('admin.usermag') }}" method="GET" class="flex">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users..." 
                                class="border border-gray-300 rounded-l-lg px-4 py-2 focus:ring focus:ring-blue-200 focus:border-blue-400 w-64" />
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-r-lg hover:bg-blue-600 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </form>
                        
                        <a href="{{ route('admin.usermag') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded-lg transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </a>
                        
                        <div class="dropdown relative ml-2">
                            <button type="button" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Export
                            </button>
                            <div id="exportDropdown" class="dropdown-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-10 border border-gray-200">
                                <a href="{{ route('admin.users.export', ['format' => 'csv', 'category' => request('category', 'users')]) }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">
                                    Export as CSV
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="mb-4 flex space-x-2">
                    <a href="{{ route('admin.usermag', array_merge(request()->except('category'), ['category' => 'users'])) }}"
                       class="px-4 py-2 rounded-lg font-semibold transition {{ request('category', 'users') == 'users' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                        Users
                    </a>
                    <a href="{{ route('admin.usermag', array_merge(request()->except('category'), ['category' => 'clinic'])) }}"
                       class="px-4 py-2 rounded-lg font-semibold transition {{ request('category') == 'clinic' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                        Clinic
                    </a>
                    <a href="{{ route('admin.usermag', array_merge(request()->except('category'), ['category' => 'doctor'])) }}"
                       class="px-4 py-2 rounded-lg font-semibold transition {{ request('category') == 'doctor' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                        Doctor
                    </a>
                    <a href="{{ route('admin.usermag', array_merge(request()->except('category'), ['category' => 'admin'])) }}"
                       class="px-4 py-2 rounded-lg font-semibold transition {{ request('category') == 'admin' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
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
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <button id="selectAllBtn" type="button" class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-lg">
                            Select All
                        </button>
                        <button id="deselectAllBtn" type="button" class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-lg">
                            Deselect All
                        </button>
                    </div>
                    
                    <div class="bulk-actions hidden items-center space-x-2" id="bulkActionsContainer">
                        <span id="selectedCount" class="text-sm font-medium text-gray-700">0 users selected</span>
                        
                        <div class="dropdown relative">
                            <button type="button" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
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
                
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                            <thead class="bg-gray-100 text-gray-700 text-sm uppercase tracking-wide">
                                <tr>
                                    <th class="px-4 py-3 w-8">
                                        <div class="flex items-center">
                                            <input type="checkbox" id="selectAll" class="form-checkbox h-4 w-4 text-blue-600 transition duration-150 ease-in-out">
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 text-left">#</th>
                                    <th class="px-6 py-3 text-left">Full Name</th>
                                    <th class="px-6 py-3 text-left">Email</th>
                                    <th class="px-6 py-3 text-left">Phone</th>
                                    <th class="px-6 py-3 text-left">Role</th>
                                    @if(request('category') == 'doctor')
                                    <th class="px-6 py-3 text-left">Clinic</th>
                                    @endif
                                    <th class="px-6 py-3 text-left">Registered At</th>
                                    <th class="px-6 py-3 text-center">Actions</th>
                                </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-gray-700">
                            @php
                                $category = request('category', 'users');
                            @endphp
                            @forelse ($users as $index => $user)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center">
                                            <input type="checkbox" name="selected_users[]" value="{{ $user->id }}" class="user-checkbox form-checkbox h-4 w-4 text-blue-600 transition duration-150 ease-in-out">
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 font-medium">{{ $user->first_name }} {{ $user->middle_name ? $user->middle_name . ' ' : '' }}{{ $user->last_name }}</td>
                                        <td class="px-6 py-4">{{ $user->email }}</td>
                                        <td class="px-6 py-4">{{ $user->phone_number ?? '—' }}</td>
                                        <td class="px-6 py-4">
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
                                        <td class="px-6 py-4">
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
                                        <td class="px-6 py-4 text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 flex items-center justify-center space-x-2">
                                            <!-- Edit Button -->
                                            <button type="button" onclick="openModal('editModal-{{ $user->id }}')"
                                                class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-4 py-2 rounded-lg shadow">
                                                Edit
                                            </button>

                                            <!-- Delete Form -->
                                            <form action="{{ route('admin.users.delete', $user->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="bg-red-600 hover:bg-red-500 text-white text-sm px-4 py-2 rounded-lg shadow-md transition">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Edit Modal -->
                                    <div id="editModal-{{ $user->id }}" 
                                        class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50">

                                        <!-- Draggable Modal -->
                                        <div id="draggable-{{ $user->id }}" 
                                            class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6 relative cursor-move"
                                            style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%);">

                                            <!-- Close button -->
                                            <button onclick="closeModal('editModal-{{ $user->id }}')"
                                                class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-2xl">
                                                &times;
                                            </button>

                                            <h2 class="text-xl font-semibold text-gray-800 mb-4">Edit User</h2>

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

                                                <div class="grid grid-cols-3 gap-4">
                                                    <div>
                                                        <label class="block text-gray-600 mb-1">First Name</label>
                                                        <input type="text" name="first_name" value="{{ $user->first_name }}"
                                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                                                    </div>
                                                    <div>
                                                        <label class="block text-gray-600 mb-1">Middle Name</label>
                                                        <input type="text" name="middle_name" value="{{ $user->middle_name }}"
                                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                                                    </div>
                                                    <div>
                                                        <label class="block text-gray-600 mb-1">Last Name</label>
                                                        <input type="text" name="last_name" value="{{ $user->last_name }}"
                                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-gray-600 mb-1">Email</label>
                                                        <input type="email" name="email" value="{{ $user->email }}"
                                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                                                    </div>
                                                    <div>
                                                        <label class="block text-gray-600 mb-1">Phone Number</label>
                                                        <input type="text" name="phone_number" value="{{ $user->phone_number }}"
                                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-gray-600 mb-1">Birthday</label>
                                                        <input type="date" name="birthday" value="{{ $user->birthday }}"
                                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                                                    </div>
                                                    <div>
                                                        <label class="block text-gray-600 mb-1">Gender</label>
                                                        <select name="gender" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                                                            <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Male</option>
                                                            <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Female</option>
                                                            <option value="prefer_not_say" {{ $user->gender == 'prefer_not_say' ? 'selected' : '' }}>Prefer not to say</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-gray-600 mb-1">Role</label>
                                                    <select name="role" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                                                        <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                                        <option value="clinic" {{ $user->role == 'clinic' ? 'selected' : '' }}>Clinic Staff</option>
                                                        <option value="doctor" {{ $user->role == 'doctor' ? 'selected' : '' }}>Doctor</option>
                                                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="block text-gray-600 mb-1">Password (leave blank if unchanged)</label>
                                                    <input type="password" name="password"
                                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                                                    <input type="password" name="password_confirmation" placeholder="Confirm password"
                                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-2 focus:ring focus:ring-blue-200">
                                                </div>

                                                <div class="flex justify-end space-x-2">
                                                    <button type="button" onclick="closeModal('editModal-{{ $user->id }}')"
                                                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg shadow mt-3">
                                                        Cancel
                                                    </button>
                                                    <button type="submit"
                                                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg shadow mt-3">
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
                    @if(method_exists($users, 'links'))
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

        // Make modals draggable
        document.querySelectorAll("[id^='draggable-']").forEach(modal => {
            let isDragging = false;
            let offsetX, offsetY;

            modal.addEventListener("mousedown", (e) => {
                // Only drag if clicked outside inputs/forms/buttons
                if (e.target.closest("input, button, form, textarea, label")) return;

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
        });

        // Bulk Actions and Export Functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Export dropdown
            const exportBtn = document.querySelector('.dropdown button');
            const exportDropdown = document.getElementById('exportDropdown');
            
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
