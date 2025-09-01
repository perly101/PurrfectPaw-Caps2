<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-900">
            Error - Clinic Not Found
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-lg p-8 mb-6 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-red-500 mx-auto mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Clinic Not Found</h2>
                
                <p class="text-gray-600 mb-8">
                    Sorry, we couldn't find the clinic you're looking for. The clinic may not exist or may have been removed.
                </p>
                
                <div class="flex justify-center">
                    <a href="{{ route('home') }}" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Return to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
