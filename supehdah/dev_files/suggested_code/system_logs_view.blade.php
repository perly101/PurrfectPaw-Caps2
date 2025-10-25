<x-app-layout>
    <div class="flex min-h-screen bg-gray-100">
        {{-- Sidebar (direct include) --}}
        @include('admin.components.sidebar')

        {{-- Main Content --}}
        <div class="flex-1 p-6 ml-64">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">System Logs</h2>
                        <p class="text-gray-500 text-sm mt-1">Review system activity and user actions</p>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <form action="{{ route('admin.system.logs') }}" method="GET" class="flex">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search logs..." 
                                class="border border-gray-300 rounded-l-lg px-4 py-2 focus:ring focus:ring-blue-200 focus:border-blue-400 w-64" />
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-r-lg hover:bg-blue-600 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </form>
                        
                        <a href="{{ route('admin.system.logs') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded-lg transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Filter Options -->
                <div class="mb-6 flex flex-wrap gap-2">
                    <div>
                        <label for="logType" class="block text-sm font-medium text-gray-700 mb-1">Log Type</label>
                        <select id="logType" name="logType" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                            <option value="">All Types</option>
                            <option value="login">Login Activity</option>
                            <option value="data">Data Changes</option>
                            <option value="system">System Events</option>
                        </select>
                    </div>

                    <div>
                        <label for="dateRange" class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                        <select id="dateRange" name="dateRange" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                            <option value="today">Today</option>
                            <option value="week">Last 7 Days</option>
                            <option value="month">Last 30 Days</option>
                            <option value="all">All Time</option>
                        </select>
                    </div>

                    <div class="ml-auto">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Actions</label>
                        <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                            Export Logs
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                        <thead class="bg-gray-100 text-gray-700 text-sm uppercase tracking-wide">
                            <tr>
                                <th class="px-6 py-3 text-left">Date & Time</th>
                                <th class="px-6 py-3 text-left">User</th>
                                <th class="px-6 py-3 text-left">IP Address</th>
                                <th class="px-6 py-3 text-left">Action</th>
                                <th class="px-6 py-3 text-left">Details</th>
                                <th class="px-6 py-3 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-gray-700">
                            @if(count($logs) > 0)
                                @foreach($logs as $log)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                                    <td class="px-6 py-4">{{ $log->user ? $log->user->email : 'System' }}</td>
                                    <td class="px-6 py-4">{{ $log->ip_address }}</td>
                                    <td class="px-6 py-4">{{ $log->action }}</td>
                                    <td class="px-6 py-4">
                                        <button onclick="showDetails('{{ $log->id }}')" class="text-blue-600 hover:text-blue-800 underline">
                                            View Details
                                        </button>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($log->status == 'success')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Success</span>
                                        @elseif($log->status == 'warning')
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">Warning</span>
                                        @elseif($log->status == 'error')
                                            <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">Error</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded-full">Info</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No logs found matching your criteria.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    @if(method_exists($logs, 'links'))
                        {{ $logs->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Log Details Modal --}}
    <div id="detailsModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl p-6 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Log Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>

            <div id="logDetails" class="border rounded-lg p-4 bg-gray-50 font-mono text-sm overflow-auto max-h-96">
                <!-- Log details will be inserted here -->
            </div>

            <div class="mt-6 flex justify-end">
                <button onclick="closeModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        function showDetails(logId) {
            // In a real implementation, you would fetch the details via AJAX
            document.getElementById('logDetails').innerHTML = `
                <p class="mb-2"><strong>Log ID:</strong> ${logId}</p>
                <p class="mb-2"><strong>Timestamp:</strong> 2023-08-24 14:35:22</p>
                <p class="mb-2"><strong>User:</strong> admin@example.com</p>
                <p class="mb-2"><strong>IP Address:</strong> 192.168.1.1</p>
                <p class="mb-2"><strong>Action:</strong> User Update</p>
                <p class="mb-2"><strong>Details:</strong></p>
                <pre class="bg-gray-100 p-3 rounded overflow-x-auto">
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
    "timestamp": "2023-08-24T14:35:22.000000Z"
}
                </pre>
            `;
            document.getElementById('detailsModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('detailsModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
