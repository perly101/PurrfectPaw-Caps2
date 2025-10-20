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
                    <h2 class="text-xl md:text-2xl font-semibold text-gray-800">Admin Dashboard</h2>
                    <p class="text-gray-500 text-sm mt-1">Overview of system metrics and performance</p>
                </div>
                
                <div class="flex space-x-2">
                    <button onclick="refreshStats()" class="flex items-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span class="hidden sm:inline">Refresh Data</span>
                        <span class="sm:hidden">Refresh</span>
                    </button>
                </div>
            </div>

                {{-- Transaction Management Card --}}
                <div class="mb-6">
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 flex items-center shadow-md rounded-lg">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="flex items-center">
                                <h3 class="text-lg font-medium text-blue-800">Transaction Management</h3>
                            </div>
                            <div class="mt-1">
                                <p class="text-sm text-blue-700">
                                    Manage clinic subscription transactions and payment history.
                                </p>
                            </div>
                            <div class="mt-3 flex space-x-2">
                                <a href="{{ route('admin.subscriptions.all') }}" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg text-sm font-medium inline-flex items-center transition duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                    </svg>
                                    View All Transactions
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($pendingPaymentsCount > 0)
                {{-- Pending Payments Alert --}}
                <div class="mb-6">
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 flex items-center shadow-md rounded-lg">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-yellow-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="flex items-center">
                                <h3 class="text-lg font-medium text-yellow-800">Pending Payments Require Attention</h3>
                                <span class="ml-2 bg-yellow-200 text-yellow-800 text-sm font-semibold px-2.5 py-0.5 rounded-full">{{ $pendingPaymentsCount }}</span>
                            </div>
                            <div class="mt-1">
                                <p class="text-sm text-yellow-700">
                                    {{ $pendingPaymentsCount }} {{ Str::plural('clinic', $pendingPaymentsCount) }} {{ $pendingPaymentsCount == 1 ? 'has' : 'have' }} made payments that require your confirmation.
                                </p>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('admin.subscriptions.pending') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded-lg text-sm font-medium inline-flex items-center transition duration-150">
                                    <span>Review Pending Payments</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                {{-- Stats Overview --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-8">
                    <div class="bg-white shadow-lg rounded-xl p-4 md:p-6 border border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-gray-700 font-medium">Total Users</h3>
                            <span class="p-2 bg-blue-100 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </span>
                        </div>
                        <div class="flex items-baseline">
                            <p class="text-2xl md:text-3xl font-bold text-gray-800" id="user-count">{{ \App\Models\User::where('role','user')->count() }}</p>
                            <span class="ml-2 text-xs md:text-sm text-gray-500">registered users</span>
                        </div>
                    </div>

                    <div class="bg-white shadow-lg rounded-xl p-4 md:p-6 border border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-gray-700 font-medium">Total Clinics</h3>
                            <span class="p-2 bg-purple-100 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </span>
                        </div>
                        <div class="flex items-baseline">
                            <p class="text-2xl md:text-3xl font-bold text-gray-800" id="clinic-count">{{ \App\Models\User::where('role','clinic')->count() }}</p>
                            <span class="ml-2 text-xs md:text-sm text-gray-500">registered clinics</span>
                        </div>
                    </div>

                    <div class="bg-white shadow-lg rounded-xl p-4 md:p-6 border border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-gray-700 font-medium">New Registrations</h3>
                            <span class="p-2 bg-green-100 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </span>
                        </div>
                        <div class="flex items-baseline">
                            <p class="text-2xl md:text-3xl font-bold text-gray-800" id="today-count">{{ \App\Models\User::whereDate('created_at', today())->count() }}</p>
                            <span class="ml-2 text-xs md:text-sm text-gray-500">today</span>
                        </div>
                    </div>
                </div>

                {{-- Recent Users --}}
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg md:text-xl font-semibold text-gray-800">Recent Users</h2>
                        <a href="{{ route('admin.usermag') }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                            <span>View All</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th scope="col" class="px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Email</th>
                                        <th scope="col" class="px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                        <th scope="col" class="px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Joined</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach (\App\Models\User::latest()->take(5)->get() as $user)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-3 md:px-6 py-2 md:py-4 whitespace-nowrap">
                                            <div class="text-xs sm:text-sm font-medium text-gray-900">{{ $user->first_name }} {{ $user->middle_name ? $user->middle_name . ' ' : '' }}{{ $user->last_name }}</div>
                                            <div class="text-xs text-gray-500 md:hidden">{{ $user->email }}</div>
                                        </td>
                                        <td class="px-3 md:px-6 py-2 md:py-4 whitespace-nowrap hidden md:table-cell">
                                            <div class="text-xs sm:text-sm text-gray-500">{{ $user->email }}</div>
                                        </td>
                                        <td class="px-3 md:px-6 py-2 md:py-4 whitespace-nowrap">
                                            <span class="px-1.5 md:px-2 py-0.5 md:py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $user->role == 'admin' ? 'bg-red-100 text-red-800' : 
                                               ($user->role == 'clinic' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800') }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td class="px-3 md:px-6 py-2 md:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500 hidden sm:table-cell">
                                            {{ $user->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Charts --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
                    {{-- User & Clinic Registrations --}}
                    <div class="bg-white shadow-lg rounded-xl p-4 md:p-6 lg:col-span-2 border border-gray-200">
                        <h3 class="text-base md:text-lg font-semibold mb-3 md:mb-4 text-gray-800">Monthly Registrations</h3>
                        <div class="relative" style="height:200px; min-height:200px; max-height:300px;">
                            <canvas id="userChart"></canvas>
                        </div>
                    </div>

                    {{-- Quick Stats --}}
                    <div class="bg-white shadow-lg rounded-xl p-4 md:p-6 border border-gray-200">
                        <h3 class="text-base md:text-lg font-semibold mb-3 md:mb-4 text-gray-800">Activity Summary</h3>
                        <div class="space-y-3 md:space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-xs md:text-sm text-gray-600">Active Users (Last 7 days)</span>
                                <span class="font-semibold text-xs md:text-sm text-gray-800">{{ rand(30, 100) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs md:text-sm text-gray-600">New Appointments</span>
                                <span class="font-semibold text-xs md:text-sm text-gray-800">{{ rand(5, 25) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs md:text-sm text-gray-600">Pending Approvals</span>
                                <span class="font-semibold text-xs md:text-sm text-gray-800">{{ rand(0, 10) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs md:text-sm text-gray-600">System Uptime</span>
                                <span class="font-semibold text-xs md:text-sm text-gray-800">99.9%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Mobile sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileSidebar = document.getElementById('mobile-sidebar');
            const sidebarContent = document.getElementById('sidebar-content');
            const closeSidebar = document.getElementById('close-sidebar');
            const sidebarBackdrop = document.getElementById('sidebar-backdrop');
            
            if (mobileMenuButton && mobileSidebar && closeSidebar && sidebarBackdrop) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileSidebar.classList.remove('hidden');
                    sidebarContent.classList.remove('-translate-x-full');
                });
                
                function hideSidebar() {
                    sidebarContent.classList.add('-translate-x-full');
                    setTimeout(() => {
                        mobileSidebar.classList.add('hidden');
                    }, 300);
                }
                
                closeSidebar.addEventListener('click', hideSidebar);
                sidebarBackdrop.addEventListener('click', hideSidebar);
            }
        });

        // Function to refresh dashboard statistics
        function refreshStats() {
            fetch('/admin/dashboard/refresh-stats')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('user-count').textContent = data.userCount;
                    document.getElementById('clinic-count').textContent = data.clinicCount;
                    document.getElementById('today-count').textContent = data.todayCount;
                    
                    // Show notification
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-4 right-4 z-50 bg-green-500 text-white px-3 py-1 md:px-4 md:py-2 rounded-lg shadow-lg transition-opacity duration-500';
                    notification.textContent = 'Statistics refreshed!';
                    document.body.appendChild(notification);
                    
                    // Remove notification after 3 seconds
                    setTimeout(() => {
                        notification.style.opacity = '0';
                        setTimeout(() => notification.remove(), 500);
                    }, 3000);
                });
        }
        
        // Adjust chart options based on screen size
        function getChartOptions() {
            const isMobile = window.innerWidth < 768;
            
            return {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: isMobile ? 'bottom' : 'top',
                        align: isMobile ? 'center' : 'center',
                        labels: {
                            boxWidth: isMobile ? 12 : 20,
                            padding: isMobile ? 10 : 20,
                            usePointStyle: true,
                            font: {
                                size: isMobile ? 10 : 12
                            }
                        }
                    },
                    tooltip: {
                        titleFont: {
                            size: isMobile ? 10 : 14
                        },
                        bodyFont: {
                            size: isMobile ? 9 : 12
                        },
                        displayColors: true,
                        padding: isMobile ? 6 : 10
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: isMobile ? 9 : 12
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: isMobile ? 9 : 12
                            },
                            maxRotation: isMobile ? 90 : 0,
                            minRotation: isMobile ? 45 : 0
                        }
                    }
                }
            };
        }

        // Users vs Clinics by Month
        let ctx = document.getElementById('userChart').getContext('2d');
        let userChart;
        
        fetch(`/admin/user-stats/month`)
            .then(res => res.json())
            .then(data => {
                userChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Users',
                                data: data.users,
                                backgroundColor: 'rgba(79, 70, 229, 0.6)',
                                borderColor: 'rgba(79, 70, 229, 1)',
                                borderWidth: 1,
                                barPercentage: window.innerWidth < 768 ? 0.9 : 0.7,
                                categoryPercentage: window.innerWidth < 768 ? 0.9 : 0.8
                            },
                            {
                                label: 'Clinics',
                                data: data.clinics,
                                backgroundColor: 'rgba(124, 58, 237, 0.6)',
                                borderColor: 'rgba(124, 58, 237, 1)',
                                borderWidth: 1,
                                barPercentage: window.innerWidth < 768 ? 0.9 : 0.7,
                                categoryPercentage: window.innerWidth < 768 ? 0.9 : 0.8
                            }
                        ]
                    },
                    options: getChartOptions()
                });
            });
            
        // Update chart options when window is resized
        window.addEventListener('resize', function() {
            if (userChart) {
                userChart.options = getChartOptions();
                userChart.update();
            }
        });
    </script>
</x-app-layout>
