{{-- resources/views/clinic/receipt.blade.php --}}

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

    <div class="py-6 bg-gray-100 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
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
            
            <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 no-print">
                <h1 class="text-2xl font-semibold text-gray-900">Subscription Receipt</h1>
                <div class="flex flex-wrap gap-2">
                    <form action="{{ route('clinic.subscription.email') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Email Receipt
                        </button>
                    </form>
                    
                    <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Receipt
                    </button>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg print-section">
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
                        </div>
                    </div>
                </div>

                <!-- Clinic & Subscription Info -->
                <div class="p-6 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Clinic Information</h3>
                            <p class="font-medium text-gray-900">{{ $subscription->clinic->clinic_name }}</p>
                            <p class="text-gray-600">{{ $subscription->clinic->address }}</p>
                            <p class="text-gray-600">{{ $subscription->clinic->contact_number }}</p>
                            <p class="text-gray-600">{{ $subscription->clinic->owner->email }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Subscription Details</h3>
                            <p class="font-medium">Plan: <span class="font-semibold text-gray-800">{{ ucfirst($subscription->plan_type) }} Plan</span></p>
                            <p class="font-medium">Status: 
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            </p>
                            <p class="font-medium">Billing Cycle: {{ ucfirst($subscription->billing_cycle) }}</p>
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
                                    <p class="text-sm text-gray-500">Reference: {{ $subscription->payment_reference ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Transaction Details</h3>
                            <p><span class="text-gray-500">Transaction Date:</span> {{ $subscription->updated_at->format('F d, Y h:i A') }}</p>
                            <p><span class="text-gray-500">Processed by:</span> {{ $subscription->status === 'active' ? 'Admin' : 'Pending Confirmation' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="p-6">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Terms & Conditions</h3>
                    <div class="text-xs text-gray-500">
                        <p class="mb-1">1. Subscription fees are non-refundable once activated.</p>
                        <p class="mb-1">2. Renewal will be processed automatically unless cancelled before the end date.</p>
                        <p class="mb-1">3. For cancellation or changes to your subscription, please contact our support team.</p>
                        <p>4. For any questions regarding your subscription, please email support@purrfectpaw.com</p>
                    </div>
                    
                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-500">Thank you for choosing PurrfectPaw Veterinary Management System!</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 print:hidden">
                <a href="{{ route('clinic.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Back to Dashboard
                </a>
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