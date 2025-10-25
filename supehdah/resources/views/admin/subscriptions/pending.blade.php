{{-- resources/views/admin/subscriptions/pending.blade.php --}}

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
                    <h2 class="text-xl md:text-2xl font-semibold text-gray-800">Pending Subscription Payments</h2>
                    <p class="text-gray-500 text-sm mt-1">Review and confirm payments from newly registered clinics</p>
                </div>
                
                <div class="flex space-x-2">
                    <a href="{{ route('admin.subscriptions.pending') }}" class="flex items-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
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

            <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-800">Pending Payments</h3>
                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $pendingSubscriptions->count() }} {{ Str::plural('payment', $pendingSubscriptions->count()) }}</span>
                </div>
                
                <div class="p-4">
                    @if($pendingSubscriptions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clinic Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan Type</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference Number</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submission Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($pendingSubscriptions as $subscription)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $subscription->clinic->clinic_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($subscription->plan_type) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">₱{{ number_format($subscription->amount, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($subscription->payment_method ?? 'GCash') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-medium bg-yellow-50 text-yellow-800 border border-yellow-200">
                                                {{ $subscription->payment_reference ?? 'No Reference' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $subscription->created_at->format('M d, Y h:i A') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="openModal('detailsModal{{ $subscription->id }}')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Quick View
                                                </button>
                                                
                                                <a href="{{ route('admin.subscriptions.show', $subscription->id) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    Full Receipt
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $pendingSubscriptions->links() }}
                        </div>
                
                @foreach($pendingSubscriptions as $subscription)
                <!-- Tailwind Modal -->
                <div id="detailsModal{{ $subscription->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-center justify-center min-h-screen px-4 text-center">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal('detailsModal{{ $subscription->id }}')"></div>
                        
                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="flex items-start justify-between pb-3 border-b border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Subscription Details</h3>
                                    <button type="button" class="bg-white rounded-md text-gray-400 hover:text-gray-500" onclick="closeModal('detailsModal{{ $subscription->id }}')">
                                        <span class="sr-only">Close</span>
                                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                
                                <div class="mt-5">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <h4 class="font-medium text-gray-800 mb-3">Clinic Information</h4>
                                            <div class="space-y-2">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600">Clinic Name:</span>
                                                    <span class="font-medium">{{ $subscription->clinic->clinic_name }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600">Address:</span>
                                                    <span>{{ $subscription->clinic->address }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600">Contact:</span>
                                                    <span>{{ $subscription->clinic->contact_number }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600">Registration Date:</span>
                                                    <span>{{ $subscription->created_at->format('F d, Y h:i A') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <h4 class="font-medium text-gray-800 mb-3">Owner Information</h4>
                                            <div class="space-y-2">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600">Name:</span>
                                                    <span class="font-medium">{{ $subscription->clinic->owner->first_name }} {{ $subscription->clinic->owner->last_name }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600">Email:</span>
                                                    <span>{{ $subscription->clinic->owner->email }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600">Phone:</span>
                                                    <span>{{ $subscription->clinic->owner->phone_number ?? 'Not provided' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <h4 class="font-medium text-gray-800 mb-3">Subscription Details</h4>
                                            <div class="space-y-2">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600">Plan Type:</span>
                                                    <span>{{ ucfirst($subscription->plan_type) }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600">Billing Cycle:</span>
                                                    <span>{{ ucfirst($subscription->billing_cycle) }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600">Amount:</span>
                                                    <span class="font-medium">₱{{ number_format($subscription->amount, 2) }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600">Payment Method:</span>
                                                    <span>{{ ucfirst($subscription->payment_method ?? 'GCash') }}</span>
                                                </div>
                                                <div class="mt-4 pt-3 border-t border-gray-200">
                                                    <h4 class="font-medium text-gray-800 mb-2">GCash Reference Number:</h4>
                                                    <div class="p-3 bg-white border-2 border-yellow-300 rounded-md">
                                                        <p class="font-mono text-center text-lg font-bold text-yellow-900">{{ $subscription->payment_reference ?? 'NOT PROVIDED' }}</p>
                                                        <p class="text-xs text-yellow-700 mt-1 text-center">Please verify this number with GCash before confirming payment</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                            <h4 class="font-medium text-gray-800 mb-3">Payment Confirmation</h4>
                                            <p class="text-gray-600 mb-4">Please verify that payment has been received before confirming this subscription.</p>
                                            
                                            <form action="{{ route('admin.subscriptions.confirm', $subscription->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                    Confirm Payment & Activate Clinic
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal('detailsModal{{ $subscription->id }}')">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                
            @else
                    <div class="text-center py-10">
                        <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No pending payments</h3>
                        <p class="mt-1 text-sm text-gray-500">There are no pending subscription payments at this time.</p>
                    </div>
                @endif
            </div>
        </div>
        
        <script>
            // Modal functions
            function openModal(modalId) {
                document.getElementById(modalId).classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
            
            function closeModal(modalId) {
                document.getElementById(modalId).classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        </script>
    </div>
</div>
</x-app-layout>