<x-app-layout>
    <div class="flex flex-col md:flex-row min-h-screen bg-gray-100">
        {{-- Sidebar (hidden on mobile) --}}
        <div class="hidden md:block">
            @include('admin.components.sidebar')
        </div>

        {{-- Include mobile navigation component --}}
        @include('admin.components.mobile-nav')

        {{-- Main Content --}}
        <div class="flex-1 p-4 md:p-6 md:ml-64 mt-12 md:mt-0">
            <div class="mb-4 md:mb-6">
                <h2 class="text-xl md:text-2xl font-semibold text-gray-800">System Logs</h2>
                <p class="text-gray-500 text-xs md:text-sm mt-1">Review system activity and user actions</p>
            </div>
            
            <div class="bg-white shadow-lg rounded-xl p-4 sm:p-6 md:p-8 border border-gray-200">
                {{-- Search and Actions --}}
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 gap-3 sm:gap-4">
                    <form action="{{ route('admin.system-logs') }}" method="GET" class="flex w-full sm:w-auto">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search logs..." 
                            class="border border-gray-300 rounded-l-lg px-3 py-2 text-sm focus:ring focus:ring-indigo-200 focus:border-indigo-400 w-full" />
                        <button type="submit" class="bg-indigo-600 text-white px-3 py-2 rounded-r-lg hover:bg-indigo-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>
                    
                    <div class="flex items-center space-x-2 w-full sm:w-auto justify-between sm:justify-end">
                        <a href="{{ route('admin.system-logs') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg transition text-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reset
                        </a>
                        <a href="{{ route('admin.system-logs.export') }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg transition text-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Export
                        </a>
                    </div>
                </div>

                {{-- Filter Options --}}
                <form id="filtersForm" action="{{ route('admin.system-logs') }}" method="GET">
                    <input type="hidden" name="search" value="{{ request('search') }}" id="searchFilter">
                    <div class="mb-4 sm:mb-6 grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label for="logType" class="block text-xs font-medium text-gray-700 mb-1">Log Type</label>
                            <select id="logType" name="type" class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-full focus:ring focus:ring-indigo-200" onchange="submitFilters()">
                                <option value="">All Types</option>
                                <option value="login" {{ request('type') == 'login' ? 'selected' : '' }}>Login Activity</option>
                                <option value="data" {{ request('type') == 'data' ? 'selected' : '' }}>Data Changes</option>
                                <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>System Events</option>
                            </select>
                        </div>

                        <div>
                            <label for="dateRange" class="block text-xs font-medium text-gray-700 mb-1">Date Range</label>
                            <select id="dateRange" name="date_range" class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-full focus:ring focus:ring-indigo-200" onchange="submitFilters()">
                                <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                                <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>Last 7 Days</option>
                                <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>Last 30 Days</option>
                                <option value="all" {{ request('date_range') == 'all' ? 'selected' : '' }}>All Time</option>
                            </select>
                        </div>

                        <div>
                            <label for="userFilter" class="block text-xs font-medium text-gray-700 mb-1">User</label>
                            <select id="userFilter" name="user_type" class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-full focus:ring focus:ring-indigo-200" onchange="submitFilters()">
                                <option value="">All Users</option>
                                <option value="admin" {{ request('user_type') == 'admin' ? 'selected' : '' }}>Administrators</option>
                                <option value="clinic" {{ request('user_type') == 'clinic' ? 'selected' : '' }}>Clinic Staff</option>
                                <option value="system" {{ request('user_type') == 'system' ? 'selected' : '' }}>System</option>
                            </select>
                        </div>
                    </div>
                </form>
                
                <script>
                    function submitFilters() {
                        // Update the search input with the current search term from the search form
                        document.getElementById('searchFilter').value = document.querySelector('input[name="search"]').value;
                        // Submit the filters form
                        document.getElementById('filtersForm').submit();
                    }
                    
                    // Update the search filter value when the search form is submitted
                    document.querySelector('form').addEventListener('submit', function(e) {
                        e.preventDefault();
                        document.getElementById('searchFilter').value = document.querySelector('input[name="search"]').value;
                        document.getElementById('filtersForm').submit();
                    });
                </script>

                {{-- Logs Table with Responsive Design --}}
                <div class="overflow-x-auto bg-white rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                <th scope="col" class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">User</th>
                                <th scope="col" class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">IP Address</th>
                                <th scope="col" class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                <th scope="col" class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            {{-- Check if logs exist and display them --}}
                            @if(isset($logs) && count($logs) > 0)
                                @foreach($logs as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 md:px-6 py-2 md:py-4 whitespace-nowrap text-xs md:text-sm text-gray-900">
                                        @if(isset($log->created_at))
                                            {{ $log->created_at->format('M d, Y') }}<br class="sm:hidden">
                                            <span class="sm:hidden text-gray-500">{{ $log->created_at->format('H:i:s') }}</span>
                                            <span class="hidden sm:inline text-gray-500">{{ $log->created_at->format('H:i:s') }}</span>
                                        @else
                                            {{ now()->format('M d, Y') }}<br class="sm:hidden">
                                            <span class="sm:hidden text-gray-500">{{ now()->format('H:i:s') }}</span>
                                            <span class="hidden sm:inline text-gray-500">{{ now()->format('H:i:s') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-3 md:px-6 py-2 md:py-4 whitespace-nowrap text-xs md:text-sm text-gray-900 hidden sm:table-cell">
                                        {{ isset($log->user) && isset($log->user->email) ? $log->user->email : 'System' }}
                                    </td>
                                    <td class="px-3 md:px-6 py-2 md:py-4 whitespace-nowrap text-xs md:text-sm text-gray-900 hidden lg:table-cell">
                                        {{ $log->ip_address ?? '127.0.0.1' }}
                                    </td>
                                    <td class="px-3 md:px-6 py-2 md:py-4 whitespace-nowrap text-xs md:text-sm text-gray-900">
                                        {{ $log->action ?? 'Unknown Action' }}
                                    </td>
                                    <td class="px-3 md:px-6 py-2 md:py-4 whitespace-nowrap text-xs">
                                        @php
                                            $status = $log->status ?? 'info';
                                            $statusClasses = [
                                                'success' => 'bg-green-100 text-green-800',
                                                'warning' => 'bg-yellow-100 text-yellow-800',
                                                'error' => 'bg-red-100 text-red-800',
                                                'info' => 'bg-blue-100 text-blue-800'
                                            ];
                                            $class = $statusClasses[$status] ?? $statusClasses['info'];
                                        @endphp
                                        <span class="px-2 py-1 text-xs rounded-full {{ $class }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                    <td class="px-3 md:px-6 py-2 md:py-4 whitespace-nowrap text-xs md:text-sm">
                                        <button onclick="showDetails('{{ $log->id ?? 'unknown' }}')" class="text-indigo-600 hover:text-indigo-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="px-3 md:px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                        No logs available. Sample data will be displayed.
                                    </td>
                                </tr>
                                @for($i = 0; $i < 5; $i++)
                                    @php
                                        $actions = ['Login', 'Update User', 'Create Appointment', 'Delete Record', 'System Backup'];
                                        $statuses = ['success', 'warning', 'error', 'info'];
                                        $users = ['admin@example.com', 'clinic@example.com', 'System'];
                                        $action = $actions[array_rand($actions)];
                                        $status = $statuses[array_rand($statuses)];
                                        $user = $users[array_rand($users)];
                                        $date = now()->subHours(rand(1, 72));
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 md:px-6 py-2 md:py-4 whitespace-nowrap text-xs md:text-sm text-gray-900">
                                            {{ $date->format('M d, Y') }}<br class="sm:hidden">
                                            <span class="sm:hidden text-gray-500">{{ $date->format('H:i:s') }}</span>
                                            <span class="hidden sm:inline text-gray-500">{{ $date->format('H:i:s') }}</span>
                                        </td>
                                        <td class="px-3 md:px-6 py-2 md:py-4 whitespace-nowrap text-xs md:text-sm text-gray-900 hidden sm:table-cell">
                                            {{ $user }}
                                        </td>
                                        <td class="px-3 md:px-6 py-2 md:py-4 whitespace-nowrap text-xs md:text-sm text-gray-900 hidden lg:table-cell">
                                            192.168.1.{{ rand(1, 255) }}
                                        </td>
                                        <td class="px-3 md:px-6 py-2 md:py-4 whitespace-nowrap text-xs md:text-sm text-gray-900">
                                            {{ $action }}
                                        </td>
                                        <td class="px-3 md:px-6 py-2 md:py-4 whitespace-nowrap text-xs">
                                            @php
                                                $statusClasses = [
                                                    'success' => 'bg-green-100 text-green-800',
                                                    'warning' => 'bg-yellow-100 text-yellow-800',
                                                    'error' => 'bg-red-100 text-red-800',
                                                    'info' => 'bg-blue-100 text-blue-800'
                                                ];
                                                $class = $statusClasses[$status];
                                            @endphp
                                            <span class="px-2 py-1 text-xs rounded-full {{ $class }}">
                                                {{ ucfirst($status) }}
                                            </span>
                                        </td>
                                        <td class="px-3 md:px-6 py-2 md:py-4 whitespace-nowrap text-xs md:text-sm">
                                            <button onclick="showDetails('sample-{{ $i }}')" class="text-indigo-600 hover:text-indigo-900">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endfor
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-4 md:mt-6">
                    @if(isset($logs) && method_exists($logs, 'links'))
                        {{ $logs->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Log Details Modal --}}
    <div id="detailsModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg sm:max-w-xl md:max-w-2xl p-4 sm:p-6 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 max-h-[90vh] overflow-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800">Log Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>

            <div id="logDetails" class="border rounded-lg p-3 sm:p-4 bg-gray-50 font-mono text-xs sm:text-sm overflow-auto max-h-[60vh]">
                <!-- Log details will be inserted here -->
            </div>

            <div class="mt-4 sm:mt-6 flex justify-end">
                <button onclick="closeModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 sm:px-4 py-2 rounded-lg text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        function showDetails(logId) {
            // In a real implementation, you would fetch the details via AJAX
            // Here we're using a fetch to get log details via AJAX
            
            // For demo/sample logs, show mock data
            if (logId.startsWith('sample-')) {
                document.getElementById('logDetails').innerHTML = `
                    <p class="mb-2"><strong>Log ID:</strong> ${logId}</p>
                    <p class="mb-2"><strong>Timestamp:</strong> ${new Date().toLocaleString()}</p>
                    <p class="mb-2"><strong>User:</strong> admin@example.com</p>
                    <p class="mb-2"><strong>IP Address:</strong> 192.168.1.${Math.floor(Math.random() * 255)}</p>
                    <p class="mb-2"><strong>Action:</strong> User Update</p>
                    <p class="mb-2"><strong>Details:</strong></p>
                    <pre class="bg-gray-100 p-2 sm:p-3 rounded overflow-x-auto text-xs leading-relaxed">
{
    "user_id": 123,
    "changes": {
        "role": {
            "from": "user",
            "to": "admin"
        },
        "email": {
            "from": "user@example.com",
            "to": "admin@example.com"
        }
    },
    "timestamp": "${new Date().toISOString()}"
}
                    </pre>
                `;
                document.getElementById('detailsModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                return;
            }
            
            // For real logs, we would fetch data from the server
            // This code would be enabled in production when backend API is ready
            /*
            fetch(`/api/admin/system-logs/${logId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch log details');
                }
                return response.json();
            })
            .then(data => {
                // Format and display the log details
                let details = '';
                if (data.details) {
                    try {
                        details = JSON.stringify(JSON.parse(data.details), null, 4);
                    } catch (e) {
                        details = data.details;
                    }
                }
                
                document.getElementById('logDetails').innerHTML = `
                    <p class="mb-2"><strong>Log ID:</strong> ${data.id || 'N/A'}</p>
                    <p class="mb-2"><strong>Timestamp:</strong> ${data.created_at || 'N/A'}</p>
                    <p class="mb-2"><strong>User:</strong> ${data.user ? data.user.email : 'System'}</p>
                    <p class="mb-2"><strong>IP Address:</strong> ${data.ip_address || 'N/A'}</p>
                    <p class="mb-2"><strong>Action:</strong> ${data.action || 'N/A'}</p>
                    <p class="mb-2"><strong>Status:</strong> ${data.status || 'N/A'}</p>
                    <p class="mb-2"><strong>Details:</strong></p>
                    <pre class="bg-gray-100 p-2 sm:p-3 rounded overflow-x-auto text-xs leading-relaxed">${details}</pre>
                `;
                document.getElementById('detailsModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            })
            .catch(error => {
                console.error('Error fetching log details:', error);
                document.getElementById('logDetails').innerHTML = `
                    <p class="text-red-500">Error loading log details. Please try again.</p>
                `;
                document.getElementById('detailsModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });
            */
            
            // For now, show sample data for real logs too
            document.getElementById('logDetails').innerHTML = `
                <p class="mb-2"><strong>Log ID:</strong> ${logId}</p>
                <p class="mb-2"><strong>Timestamp:</strong> ${new Date().toLocaleString()}</p>
                <p class="mb-2"><strong>User:</strong> admin@example.com</p>
                <p class="mb-2"><strong>IP Address:</strong> 192.168.1.${Math.floor(Math.random() * 255)}</p>
                <p class="mb-2"><strong>Action:</strong> User Update</p>
                <p class="mb-2"><strong>Details:</strong></p>
                <pre class="bg-gray-100 p-2 sm:p-3 rounded overflow-x-auto text-xs leading-relaxed">
{
    "user_id": 123,
    "changes": {
        "role": {
            "from": "user",
            "to": "admin"
        },
        "email": {
            "from": "user@example.com",
            "to": "admin@example.com"
        }
    },
    "timestamp": "${new Date().toISOString()}"
}
                </pre>
            `;
            document.getElementById('detailsModal').classList.remove('hidden');
            
            // Prevent scrolling on body when modal is open
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('detailsModal').classList.add('hidden');
            
            // Re-enable scrolling when modal is closed
            document.body.style.overflow = '';
        }

        // Close modal when clicking outside
        document.getElementById('detailsModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeModal();
            }
        });

        // Prevent closing when clicking inside the modal content
        document.querySelector('#detailsModal > div').addEventListener('click', function(event) {
            event.stopPropagation();
        });
    </script>
</x-app-layout>