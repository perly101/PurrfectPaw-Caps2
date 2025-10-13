<x-app-layout>
    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex space-x-6">
            
            {{-- Sidebar --}}
            <div class="w-1/4">
                @include('doctor.components.sidebar')
            </div>
            
            {{-- Main Content --}}
            <div class="flex-1">
                <div class="bg-white shadow-xl rounded-lg p-8">
                    <h1 class="text-2xl font-bold text-gray-800 mb-6">Patients</h1>
                    
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif
                    
                    {{-- Search --}}
                    <div class="mb-6">
                        <form method="GET" action="{{ route('doctor.patients.index') }}" class="flex gap-4">
                            <div class="flex-1">
                                <input type="text" name="search" value="{{ request()->get('search') }}" placeholder="Search by name or phone number" 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-150">
                                Search
                            </button>
                            @if(request()->has('search'))
                                <a href="{{ route('doctor.patients.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition duration-150">
                                    Clear
                                </a>
                            @endif
                        </form>
                    </div>
                    
                    {{-- Patients List --}}
                    <div class="space-y-6">
                        @forelse($patients as $patient)
                            <div class="bg-white overflow-hidden shadow rounded-lg border">
                                <a href="{{ route('doctor.patients.show', $patient->owner_phone) }}" class="block">
                                    <div class="p-5 flex justify-between items-center bg-gray-50 cursor-pointer hover:bg-gray-100 transition duration-150">
                                        <div>
                                            <h3 class="text-lg font-medium text-gray-900">{{ $patient->owner_name }}</h3>
                                            <p class="text-gray-500">{{ $patient->owner_phone }}</p>
                                        </div>
                                        <div class="text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <div class="bg-white p-6 text-center text-gray-500 rounded-lg shadow">
                                No patients found
                            </div>
                        @endforelse
                    </div>
                    
                    {{-- Pagination --}}
                    <div class="mt-6">
                        {{ $patients->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
