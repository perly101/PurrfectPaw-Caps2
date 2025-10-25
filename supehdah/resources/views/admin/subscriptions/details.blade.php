{{-- resources/views/admin/subscriptions/details.blade.php --}}

<x-app-layout>
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .print-section, .print-section * {
                visibility: visible;
            }
            .no-print {
                display: none !important;
            }
            #mobile-menu, .admin-sidebar, .navbar {
                display: none !important;
            }
            .print-section {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .print-section .card {
                box-shadow: none !important;
                border: 1px solid #eee;
            }
            @page {
                size: auto;
                margin: 20mm 15mm 20mm 15mm;
            }
        }
    </style>
    
    {{-- Include mobile navigation component --}}
    @include('admin.components.mobile-nav')
    
    <div class="flex flex-col md:flex-row min-h-screen bg-gray-100">
        {{-- Sidebar (hidden on mobile) --}}
        <div class="md:block hidden">
            @include('admin.components.sidebar')
        </div>

        {{-- Main Content --}}
        <div class="flex-1 p-4 md:p-6 md:ml-64 w-full">
            @if(session('success'))
            <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
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
            <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
            @endif
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div class="mb-4 md:mb-0">
                    <h2 class="text-xl md:text-2xl font-semibold text-gray-800">Subscription Details</h2>
                    <p class="text-gray-500 text-sm mt-1">Complete information about this clinic subscription</p>
                </div>
                
                <div class="flex flex-wrap gap-2">
                    <form action="{{ route('admin.subscriptions.email', $subscription->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>Email Receipt</span>
                        </button>
                    </form>
                    <button onclick="window.print()" class="flex items-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Print</span>
                    </button>
                    <a href="{{ route('admin.subscriptions.pending') }}" class="flex items-center px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Back</span>
                    </a>
                </div>
            </div>

            <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200 print-section">
                <!-- Receipt Header -->
                <div class="p-6 border-b border-gray-200 bg-gray-50 print:bg-white">
                    <div class="flex flex-col md:flex-row justify-between">
                        <div class="mb-4 md:mb-0">
                            <div class="flex items-center">
                                <img src="{{ asset('images/pet-logo.png') }}" alt="PurrfectPaw Logo" class="h-10 w-10 mr-3">
                                <div>
                                    <h2 class="text-xl font-bold text-gray-800">PurrfectPaw</h2>
                                    <p class="text-sm text-gray-500">Veterinary Management System</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-gray-700">
                            <p class="text-sm">Receipt #: {{ $subscription->payment_reference ?? 'RCPT-' . $subscription->id }}</p>
                            <p class="text-sm">Date: {{ now()->format('F d, Y') }}</p>
                            <p class="text-sm">Subscription ID: {{ $subscription->id }}</p>
                        </div>
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="font-medium text-gray-900">Subscription Status</h3>
                            <p class="text-sm text-gray-500">Current status of this subscription</p>
                        </div>
                        <div>
                            @if($subscription->status === 'active')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Active
                                </span>
                            @elseif($subscription->status === 'pending_admin_confirmation')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Pending Confirmation
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    @if($subscription->status === 'pending_admin_confirmation')
                    <div class="mt-4">
                        <form action="{{ route('admin.subscriptions.confirm', $subscription->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Confirm Payment & Activate Clinic
                            </button>
                        </form>
                    </div>
                    @endif
                </div>

                <!-- Clinic & Subscription Info -->
                <div class="p-6 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Clinic Information</h3>
                            <p class="font-medium text-gray-900">{{ $subscription->clinic->clinic_name }}</p>
                            <p class="text-gray-600">{{ $subscription->clinic->address }}</p>
                            <p class="text-gray-600">{{ $subscription->clinic->contact_number }}</p>
                            <p class="text-gray-600">Registration Date: {{ $subscription->clinic->created_at->format('F d, Y') }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Clinic Owner</h3>
                            <p class="font-medium text-gray-900">{{ $subscription->clinic->owner->first_name }} {{ $subscription->clinic->owner->last_name }}</p>
                            <p class="text-gray-600">{{ $subscription->clinic->owner->email }}</p>
                            <p class="text-gray-600">{{ $subscription->clinic->owner->phone_number ?? 'No phone provided' }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Subscription Dates -->
                <div class="p-6 border-b border-gray-200 bg-gray-50 print:bg-white">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Subscription Period</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Start Date</p>
                            <p class="font-medium">{{ $subscription->start_date ? $subscription->start_date->format('F d, Y') : 'Pending Activation' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">End Date</p>
                            <p class="font-medium">{{ $subscription->end_date ? $subscription->end_date->format('F d, Y') : 'Pending Activation' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Next Billing</p>
                            <p class="font-medium">{{ $subscription->next_billing_date ? $subscription->next_billing_date->format('F d, Y') : 'Pending Activation' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Payment Information</h3>
                    <div class="overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 print:bg-white">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ ucfirst($subscription->plan_type) }} Subscription
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ ucfirst($subscription->billing_cycle) }} billing cycle
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                        ₱{{ number_format($subscription->amount, 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">Total</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                        ₱{{ number_format($subscription->amount, 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="p-6 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Payment Method</h3>
                            <div class="flex items-center">
                                <div class="bg-blue-100 p-2 rounded-md mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium">{{ ucfirst($subscription->payment_method ?? 'GCash') }}</p>
                                </div>
                            </div>
                            
                            @if($subscription->status === 'pending_admin_confirmation')
                            <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                <h4 class="font-medium text-yellow-800 mb-1 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Verification Required
                                </h4>
                                <p class="text-sm text-yellow-700">Please verify this GCash reference number against your GCash transactions:</p>
                                <div class="mt-2 p-2 bg-white border border-yellow-300 rounded font-mono text-lg text-center font-bold text-yellow-900">
                                    {{ $subscription->payment_reference ?? 'No reference provided' }}
                                </div>
                                <p class="mt-2 text-xs text-yellow-700">This reference was provided by the clinic during payment submission.</p>
                            </div>
                            @else
                            <div class="mt-4">
                                <p class="text-sm text-gray-800"><span class="font-medium">Reference Number:</span> {{ $subscription->payment_reference ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500 mt-1">This reference was provided by the clinic during payment submission.</p>
                            </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Transaction Details</h3>
                            <p><span class="text-gray-500">Submitted:</span> {{ $subscription->updated_at->format('F d, Y h:i A') }}</p>
                            @if($subscription->status === 'active')
                                <p><span class="text-gray-500">Approved:</span> {{ $subscription->start_date->format('F d, Y h:i A') }}</p>
                                <p><span class="text-gray-500">Approved by:</span> Administrator</p>
                                <p class="mt-2"><span class="text-gray-500">Verified Reference Number:</span> <span class="font-medium">{{ $subscription->payment_reference }}</span></p>
                            @else
                                <p><span class="text-gray-500">Status:</span> Pending Admin Confirmation</p>
                                <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-md">
                                    <p class="text-sm text-blue-700">Please check this reference number in your GCash account before confirming payment.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Admin Notes Section (Admin Only) -->
                <div class="p-6 border-b border-gray-200 print:hidden">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Admin Notes</h3>
                    <div class="mt-1">
                        <textarea rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Add any notes about this subscription (for admin reference only)"></textarea>
                    </div>
                    <div class="mt-2 text-right">
                        <button type="button" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Save Notes
                        </button>
                    </div>
                </div>
                
                <!-- Admin Actions -->
                <div class="p-6 print:hidden">
                    <div class="flex flex-col sm:flex-row sm:justify-between space-y-3 sm:space-y-0">
                        <a href="{{ route('admin.subscriptions.pending') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                            </svg>
                            Back to All Subscriptions
                        </a>
                        
                        @if($subscription->status === 'active')
                        <!-- <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Email Receipt to Clinic
                        </button> -->
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style type="text/css" media="print">
        @page {
            size: auto;
            margin: 15mm;
        }
        
        body {
            background-color: white;
            margin: 0;
            padding: 0;
        }
        
        .print\:hidden {
            display: none !important;
        }
        
        .print\:bg-white {
            background-color: white !important;
        }
        
        .print\:shadow-none {
            box-shadow: none !important;
        }
        
        .print\:border {
            border-width: 1px !important;
        }
        
        .print\:border-gray-300 {
            border-color: #d1d5db !important;
        }
    </style>
</x-app-layout>