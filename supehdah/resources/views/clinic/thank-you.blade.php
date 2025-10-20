{{-- resources/views/clinic/thank-you.blade.php --}}

<x-app-layout>
    <div class="min-h-screen bg-gray-100 py-6 flex flex-col justify-center">
        <div class="sm:max-w-xl md:max-w-2xl mx-auto">
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Thank You!</h2>
                <p class="text-gray-500 text-base md:text-lg mt-3">Your payment has been successfully processed</p>
            </div>
            
            <div class="bg-white shadow-lg rounded-xl p-6 md:p-8 border border-gray-200">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-semibold text-gray-800">Your Clinic is Now Active</h3>
                    <p class="text-gray-600 mt-2">Welcome to PurrfectPaw Veterinary Management System</p>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-800">Important Information</h4>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>We have sent a confirmation email to your registered email address. Please check your inbox for login details and important next steps.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Account Details Summary -->
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="font-medium text-gray-800 mb-3">Clinic Details</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Clinic Name</p>
                            <p class="font-medium">{{ $subscription->clinic->clinic_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Subscription Plan</p>
                            <p class="font-medium">{{ ucfirst($subscription->plan_type) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Subscription Status</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Active
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Staff Email</p>
                            <p class="font-medium">{{ $subscription->clinic->owner->email }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('login') }}" class="inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Login to Dashboard
                    </a>
                    <a href="{{ route('landing') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Back to Home
                    </a>
                </div>
            </div>
            
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-500">Need help? <a href="#" class="text-indigo-600 hover:text-indigo-500">Contact our support team</a></p>
            </div>
        </div>
    </div>
</x-app-layout>