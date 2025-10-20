{{-- resources/views/admin/subscriptions/all.blade.php --}}

<x-app-layout>
    {{-- Include mobile navigation component --}}
    @include('admin.components.mobile-nav')
    
    <div class="flex flex-col md:flex-row min-h-screen bg-gray-100">
        {{-- Sidebar (hidden on mobile) --}}
        <div class="md:block hidden">
            @include('admin.components.sidebar')
        </div>

        {{-- Main Content --}}
        <div class="flex-1 p-4 md:p-6 md:ml-64 w-full">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div class="mb-4 md:mb-0">
                    <h2 class="text-xl md:text-2xl font-semibold text-gray-800">All Subscription Transactions</h2>
                    <p class="text-gray-500 text-sm mt-1">View and manage all clinic subscription transactions</p>
                </div>
                
                <div class="flex space-x-2">
                    <a href="{{ route('admin.subscriptions.all') }}" class="flex items-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Refresh</span>
                    </a>
                </div>
            </div>

            @if(session('success'))
            <div class="mb-4 p-4 border-l-4 border-green-500 bg-green-50 rounded-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 p-4 border-l-4 border-red-500 bg-red-50 rounded-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-11a1 1 0 112 0v4a1 1 0 11-2 0V7zm1 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Filter and Search --}}
            <div class="mb-6 bg-white shadow-sm rounded-lg p-4 border border-gray-200">
                <form action="{{ route('admin.subscriptions.all') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="w-full md:w-1/3">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Filter by Status</label>
                        <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Statuses</option>
                            <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="pending_admin_confirmation" {{ $status == 'pending_admin_confirmation' ? 'selected' : '' }}>Pending Confirmation</option>
                            <option value="expired" {{ $status == 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="canceled" {{ $status == 'canceled' ? 'selected' : '' }}>Canceled</option>
                        </select>
                    </div>
                    
                    <div class="w-full md:w-1/3">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search by Clinic Name</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="text" name="search" id="search" value="{{ request()->search }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-3 pr-10 py-2 sm:text-sm border-gray-300 rounded-md" placeholder="Enter clinic name">
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Search
                        </button>
                        
                        <a href="{{ route('admin.subscriptions.all') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-800">Subscription Transactions</h3>
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $subscriptions->total() }} {{ Str::plural('transaction', $subscriptions->total()) }}</span>
                </div>
                
                <div class="p-4">
                    @if($subscriptions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clinic Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan Type</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($subscriptions as $subscription)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $subscription->clinic->clinic_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($subscription->plan_type) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">₱{{ number_format($subscription->amount, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($subscription->status == 'active')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                            @elseif($subscription->status == 'pending_admin_confirmation')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Pending
                                                </span>
                                            @elseif($subscription->status == 'expired')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Expired
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    {{ ucfirst($subscription->status) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $subscription->created_at->format('M d, Y h:i A') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('admin.subscriptions.show', $subscription->id) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    View Receipt
                                                </a>
                                                
                                                @if($subscription->status == 'active')
                                                <form action="{{ route('admin.subscriptions.email', $subscription->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                        </svg>
                                                        Email
                                                    </button>
                                                </form>
                                                @endif
                                                
                                                @if($subscription->status == 'pending_admin_confirmation')
                                                <form action="{{ route('admin.subscriptions.confirm', $subscription->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Approve
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $subscriptions->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-10">
                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No transactions found</h3>
                            <p class="mt-1 text-sm text-gray-500">There are no subscription transactions matching your criteria.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>