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
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div class="mb-4 md:mb-0">
                    <h2 class="text-xl md:text-2xl font-semibold text-gray-800">Clinic Management</h2>
                    <p class="text-gray-500 text-sm mt-1">View and manage all registered clinics</p>
                </div>
                
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 w-full md:w-auto">
                    <form action="{{ route('admin.clinics') }}" method="GET" class="flex w-full sm:w-auto">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search clinics..." 
                            class="border border-gray-300 rounded-l-lg px-3 py-2 text-sm focus:ring focus:ring-blue-200 focus:border-blue-400 w-full sm:w-64" />
                        <button type="submit" class="bg-blue-500 text-white px-3 py-2 rounded-r-lg hover:bg-blue-600 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>
                    
                    <a href="{{ route('admin.clinics') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded-lg transition flex justify-center items-center w-full sm:w-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </a>
                </div>
            </div>
            
            <div class="bg-white shadow-lg rounded-xl px-4 sm:px-6 md:px-8 pt-4 sm:pt-6 pb-6 sm:pb-8 mb-4">

                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 sm:p-4 mb-4 sm:mb-6 text-xs sm:text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 sm:p-4 mb-4 sm:mb-6 text-xs sm:text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Clinic Name</th>
                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">Address</th>
                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden sm:table-cell">Contact</th>
                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($clinics as $index => $clinic)
                                <tr>
                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm">{{ $index + 1 }}</td>
                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm">
                                        {{ $clinic->clinic_name }}
                                        <div class="sm:hidden text-xs text-gray-500 mt-1">
                                            <div>{{ $clinic->contact_number }}</div>
                                            <div class="truncate max-w-[150px]">{{ $clinic->address }}</div>
                                        </div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm hidden md:table-cell">{{ $clinic->address }}</td>
                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm hidden sm:table-cell">{{ $clinic->contact_number }}</td>
                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm">
                                        <div class="flex flex-col sm:flex-row gap-2">
                                            <a href="{{ route('admin.clinics.view', $clinic->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1.5 rounded-md shadow-sm transition flex justify-center items-center">
                                                <span class="hidden sm:inline">View</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('admin.clinics.delete', $clinic->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this clinic? This will also delete the associated user account.');" class="w-full sm:w-auto">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-1.5 rounded-md shadow-sm transition flex justify-center items-center w-full">
                                                    <span class="hidden sm:inline">Delete</span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 sm:px-6 py-4 text-center text-gray-500 text-xs sm:text-sm">
                                        @if(request('search'))
                                            No clinics found matching "{{ request('search') }}".
                                        @else
                                            No clinics found.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                <div class="mt-6">
                    @if(method_exists($clinics, 'links'))
                        {{ $clinics->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
