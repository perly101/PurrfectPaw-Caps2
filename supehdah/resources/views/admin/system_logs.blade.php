@extends('admin.layouts.app')

@section('content')
<div class="container px-6 mx-auto grid">
    
    <h2 class="my-6 text-2xl font-semibold text-gray-700">
        System Logs
    </h2>
    
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <form action="{{ route('admin.system-logs') }}" method="GET" class="flex items-center space-x-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search logs..." 
                    class="px-3 py-2 border rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                
                <select name="status" class="px-3 py-2 border rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">All Statuses</option>
                    <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                    <option value="error" {{ request('status') == 'error' ? 'selected' : '' }}>Error</option>
                    <option value="warning" {{ request('status') == 'warning' ? 'selected' : '' }}>Warning</option>
                    <option value="info" {{ request('status') == 'info' ? 'selected' : '' }}>Info</option>
                </select>
                
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                    class="px-3 py-2 border rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                    class="px-3 py-2 border rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">
                    Filter
                </button>
                <a href="{{ route('admin.system-logs') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded text-sm hover:bg-gray-300">
                    Reset
                </a>
            </form>
        </div>
        
        <div class="flex space-x-2">
            <a href="{{ route('admin.system-logs.export', request()->all()) }}" class="px-4 py-2 bg-green-600 text-white rounded text-sm hover:bg-green-700">
                Export CSV
            </a>
            
            <form action="{{ route('admin.system-logs.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear these logs? This action cannot be undone.')">
                @csrf
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                    Clear Logs
                </button>
            </form>
        </div>
    </div>
    
    <!-- Session Message -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
        {{ session('success') }}
    </div>
    @endif
    
    <!-- Logs Table -->
    <div class="w-full overflow-hidden rounded-lg shadow-md">
        <div class="w-full overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">Action</th>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">IP Address</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Date/Time</th>
                        <th class="px-4 py-3">Details</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse($logs as $log)
                    <tr class="text-gray-700">
                        <td class="px-4 py-3 text-sm">
                            {{ $log->action }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($log->user)
                                {{ $log->user->first_name }} {{ $log->user->last_name }}
                                <span class="text-xs text-gray-500">{{ $log->user->email }}</span>
                            @else
                                System
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            {{ $log->ip_address }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @php
                                $statusColors = [
                                    'success' => 'green',
                                    'error' => 'red',
                                    'warning' => 'yellow',
                                    'info' => 'blue',
                                ];
                                $color = $statusColors[$log->status] ?? 'gray';
                            @endphp
                            <span class="px-2 py-1 font-semibold leading-tight text-{{ $color }}-700 bg-{{ $color }}-100 rounded-full">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            {{ $log->created_at->format('M d, Y H:i:s') }}
                            <span class="text-xs text-gray-500">
                                {{ $log->created_at->diffForHumans() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($log->details)
                                <button type="button" class="text-blue-600 hover:underline view-details" 
                                    data-details="{{ json_encode($log->details) }}">
                                    View Details
                                </button>
                            @else
                                <span class="text-gray-400">No details</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-center text-gray-500">
                            No logs found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-4 py-3 border-t bg-gray-50">
            {{ $logs->links() }}
        </div>
    </div>
</div>

<!-- Details Modal -->
<div id="details-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-700">Log Details</h3>
        </div>
        <div class="p-6">
            <pre id="details-content" class="bg-gray-100 p-4 rounded-lg text-sm overflow-auto max-h-80"></pre>
        </div>
        <div class="px-6 py-3 border-t flex justify-end">
            <button id="close-modal" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Close
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // View details modal
        const modal = document.getElementById('details-modal');
        const detailsContent = document.getElementById('details-content');
        const closeModalBtn = document.getElementById('close-modal');
        
        // Open modal when clicking "View Details" button
        document.querySelectorAll('.view-details').forEach(button => {
            button.addEventListener('click', function() {
                const details = JSON.parse(this.dataset.details);
                detailsContent.textContent = JSON.stringify(details, null, 2);
                modal.classList.remove('hidden');
            });
        });
        
        // Close modal when clicking the close button or outside the modal
        closeModalBtn.addEventListener('click', function() {
            modal.classList.add('hidden');
        });
        
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    });
</script>
@endsection
