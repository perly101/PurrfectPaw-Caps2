<!-- Professional Admin Sidebar -->

@php
    use App\Models\User;
    $user = auth()->user();
    $currentRoute = request()->route()->getName();
@endphp

<div class="w-64 h-screen bg-gradient-to-b from-[#1A2238] to-[#0F172A] text-gray-100 fixed top-0 left-0 shadow-xl flex flex-col">

    <!-- Admin Logo & Name -->
    <div class="flex flex-col items-center justify-center px-4 py-5 border-b border-indigo-900/40">
        <div class="flex items-center justify-center mb-2">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-0.5 rounded-lg">
                <div class="bg-gradient-to-b from-[#1A2238] to-[#0F172A] p-1 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-indigo-400" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z" />
                    </svg>
                </div>
            </div>
        </div>
        <h1 class="text-xl font-bold text-center leading-tight tracking-wide text-transparent bg-clip-text bg-gradient-to-r from-indigo-300 to-purple-300">
            SuPehDah
        </h1>
        <p class="text-xs text-indigo-300/80 font-medium mt-1">Admin Control Center</p>
    </div>

    <!-- Admin Profile -->
    <div class="px-4 py-4 border-b border-indigo-900/40 flex items-center">
        <div class="w-10 h-10 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 p-0.5">
            <div class="bg-indigo-900 rounded-full w-full h-full flex items-center justify-center">
                <span class="uppercase text-indigo-300 font-bold">{{ $user ? substr($user->first_name, 0, 1) : 'A' }}</span>
            </div>
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium text-gray-200">{{ $user ? "{$user->first_name} {$user->middle_name} {$user->last_name}" : 'Admin User' }}</p>
            <p class="text-xs text-indigo-300/70">Administrator</p>
        </div>
    </div>

    <!-- Navigation Links -->
    <nav class="mt-4 px-3 flex-1 overflow-y-auto">
        <p class="px-4 text-xs font-semibold text-indigo-300/70 uppercase tracking-wider mb-2">Main Navigation</p>
        <ul class="space-y-1 font-medium">
            <li>
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center px-4 py-3 rounded-lg {{ $currentRoute === 'admin.dashboard' ? 'bg-indigo-700/40 text-white' : 'hover:bg-indigo-800/40 hover:text-white' }} transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ $currentRoute === 'admin.dashboard' ? 'text-indigo-300' : 'text-indigo-400' }} mr-3" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                    </svg>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('step1.create') }}"
                   class="flex items-center px-4 py-3 rounded-lg {{ $currentRoute === 'step1.create' ? 'bg-indigo-700/40 text-white' : 'hover:bg-indigo-800/40 hover:text-white' }} transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ $currentRoute === 'step1.create' ? 'text-indigo-300' : 'text-indigo-400' }} mr-3" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 01-1 1v1a1 1 0 11-2 0v-1H7v1a1 1 0 11-2 0v-1a1 1 0 01-1-1V4zm3 1h6v4H7V5zm8 8V9H5v4h10z" clip-rule="evenodd" />
                    </svg>
                    <span>Register Clinic</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.usermag') }}"
                   class="flex items-center px-4 py-3 rounded-lg {{ $currentRoute === 'admin.usermag' ? 'bg-indigo-700/40 text-white' : 'hover:bg-indigo-800/40 hover:text-white' }} transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ $currentRoute === 'admin.usermag' ? 'text-indigo-300' : 'text-indigo-400' }} mr-3" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                    </svg>
                    <span>User Management</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.clinics') }}"
                   class="flex items-center px-4 py-3 rounded-lg {{ $currentRoute === 'admin.clinics' ? 'bg-indigo-700/40 text-white' : 'hover:bg-indigo-800/40 hover:text-white' }} transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ $currentRoute === 'admin.clinics' ? 'text-indigo-300' : 'text-indigo-400' }} mr-3" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>Clinic Management</span>
                </a>
            </li>

            
            
            <p class="px-4 text-xs font-semibold text-indigo-300/70 uppercase tracking-wider mt-6 mb-2">Preferences</p>

            <!-- <li>
                <a href="{{ route('admin.settings') }}"
                   class="flex items-center px-4 py-3 rounded-lg {{ $currentRoute === 'admin.settings' ? 'bg-indigo-700/40 text-white' : 'hover:bg-indigo-800/40 hover:text-white' }} transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ $currentRoute === 'admin.settings' ? 'text-indigo-300' : 'text-indigo-400' }} mr-3" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                    </svg>
                    <span>Account Settings</span>
                </a>
            </li> -->
            
            <li>
                <a href="{{ route('admin.application-settings') }}"
                   class="flex items-center px-4 py-3 rounded-lg {{ $currentRoute === 'admin.application-settings' ? 'bg-indigo-700/40 text-white' : 'hover:bg-indigo-800/40 hover:text-white' }} transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ $currentRoute === 'admin.application-settings' ? 'text-indigo-300' : 'text-indigo-400' }} mr-3" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                    </svg>
                    <span>Application Settings</span>
                </a>
            </li>
            <p class="px-4 text-xs font-semibold text-indigo-300/70 uppercase tracking-wider mt-6 mb-2">System</p>
            
            <li>
                <a href="{{ route('admin.system-logs') }}"
                   class="flex items-center px-4 py-3 rounded-lg {{ $currentRoute === 'admin.system-logs' ? 'bg-indigo-700/40 text-white' : 'hover:bg-indigo-800/40 hover:text-white' }} transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ $currentRoute === 'admin.system-logs' ? 'text-indigo-300' : 'text-indigo-400' }} mr-3" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <span>System Logs</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Footer & Logout Button -->
    <div class="mt-auto">
        <div class="px-3 py-4">
            <div class="bg-indigo-900/40 rounded-lg p-3 mb-4">
                <div class="flex items-center">
                    <div class="p-1.5 bg-indigo-500/20 rounded-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-300" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <p class="ml-2 text-xs font-medium text-indigo-200">Need help with the admin panel?</p>
                </div>
                <a href="#" class="mt-2 text-xs text-indigo-400 hover:text-indigo-300 transition-colors duration-200 block text-center">
                    View Documentation
                </a>
            </div>
        </div>
        <div class="px-3 py-3 border-t border-indigo-900/40">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 rounded-lg transition duration-200 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span>Sign Out</span>
                </button>
            </form>
        </div>
    </div>
</div>
