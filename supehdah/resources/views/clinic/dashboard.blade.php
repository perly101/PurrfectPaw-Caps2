@php
    use App\Models\ClinicInfo;
    $clinic = ClinicInfo::where('user_id', auth()->id())->first();
@endphp

<x-app-layout>
    {{-- Include mobile navigation component --}}
    @include('clinic.components.mobile-nav')
    
    <div class="flex flex-col md:flex-row min-h-screen bg-gray-100">
        {{-- Sidebar (hidden on mobile) --}}
        <div class="md:block hidden">
            @include('clinic.components.sidebar')
        </div>

        {{-- Main Content --}}  
        <div class="flex-1 p-4 md:p-6 md:ml-64 w-full">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div class="mb-4 md:mb-0">
                    <h2 class="text-xl md:text-2xl font-semibold text-gray-800">Clinic Dashboard</h2>
                    <p class="text-gray-500 text-sm mt-1">Welcome back{{ $clinic ? ', ' . $clinic->clinic_name : '' }}!</p>
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
                
                {{-- Subscription Management Card --}}
                <div class="mb-6">
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 flex items-center shadow-md rounded-lg">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="flex items-center">
                                <h3 class="text-lg font-medium text-blue-800">Subscription Status</h3>
                                @if($subscription && $subscription->status == 'active')
                                    <span class="ml-2 bg-green-100 text-green-800 text-sm font-semibold px-2.5 py-0.5 rounded-full">Active</span>
                                @elseif($subscription && $subscription->status == 'pending_admin_confirmation')
                                    <span class="ml-2 bg-yellow-100 text-yellow-800 text-sm font-semibold px-2.5 py-0.5 rounded-full">Pending</span>
                                @else
                                    <span class="ml-2 bg-red-100 text-red-800 text-sm font-semibold px-2.5 py-0.5 rounded-full">Inactive</span>
                                @endif
                            </div>
                            <div class="mt-1">
                                <p class="text-sm text-blue-700">
                                    Manage your clinic subscription and view payment history.
                                </p>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('clinic.subscription.receipt') }}" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg text-sm font-medium inline-flex items-center transition duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                    </svg>
                                    View Subscription Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stats Overview --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8">
                    <div class="bg-white shadow-lg rounded-xl p-3 border border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-gray-700 text-sm">Total Doctors</h3>
                            <span class="p-1.5 bg-blue-100 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </span>
                        </div>
                        <div class="flex items-baseline">
                            <p class="text-xl font-bold text-gray-800" id="doctor-count">{{ $doctorCount }}</p>
                            <a href="{{ route('clinic.doctors.index') }}" class="ml-auto text-blue-600 hover:text-blue-800 flex items-center">
                                <span class="text-xs">View</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <div class="bg-white shadow-lg rounded-xl p-3 border border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-gray-700 text-sm">Total Patients</h3>
                            <span class="p-1.5 bg-purple-100 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </span>
                        </div>
                        <div class="flex items-baseline">
                            <p class="text-xl font-bold text-gray-800" id="patient-count">{{ $patientCount }}</p>
                            <a href="{{ route('clinic.patients.index') }}" class="ml-auto text-purple-600 hover:text-purple-800 flex items-center">
                                <span class="text-xs">View</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="bg-white shadow-lg rounded-xl p-3 border border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-gray-700 text-sm">Today's Appts</h3>
                            <span class="p-1.5 bg-green-100 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </span>
                        </div>
                        <div class="flex items-baseline">
                            <p class="text-xl font-bold text-gray-800" id="today-count">{{ $todayAppointmentsCount }}</p>
                            <a href="{{ route('clinic.appointments.index') }}" class="ml-auto text-green-600 hover:text-green-800 flex items-center">
                                <span class="text-xs">View</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <div class="bg-white shadow-lg rounded-xl p-3 border border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-gray-700 text-sm">Pending Appts</h3>
                            <span class="p-1.5 bg-yellow-100 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                        </div>
                        <div class="flex items-baseline">
                            <p class="text-xl font-bold text-gray-800" id="pending-count">{{ $pendingAppointmentsCount }}</p>
                            <a href="{{ route('clinic.appointments.index') }}" class="ml-auto text-yellow-600 hover:text-yellow-800 flex items-center">
                                <span class="text-xs">View</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
                    <!-- Clinic Info & Stats -->
                    <div class="bg-white shadow-lg rounded-xl p-4 md:p-6 border border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-gray-700 font-medium">Clinic Information</h3>
                            <span class="p-2 bg-indigo-100 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </span>
                        </div>

                        <div class="flex items-center mb-4">
                            @if ($clinic && $clinic->profile_picture)
                                <img src="{{ asset('storage/' . $clinic->profile_picture) }}"
                                    class="w-16 h-16 rounded-full object-cover shadow border-2 border-indigo-200"
                                    alt="Clinic Profile Picture">
                            @else
                                <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                    <span class="text-xs">No Image</span>
                                </div>
                            @endif
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    {{ $clinic->clinic_name ?? 'Your Clinic' }}
                                </h3>
                                <span class="text-xs px-2 py-1 inline-flex leading-5 font-semibold rounded-full bg-{{ $clinic->is_open ? 'green' : 'red' }}-100 text-{{ $clinic->is_open ? 'green' : 'red' }}-800">
                                    {{ $clinic->is_open ? 'Currently Open' : 'Currently Closed' }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-3 mb-4">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="text-sm text-gray-700">{{ $clinic->address ?? 'Address not provided' }}</span>
                            </div>
                            
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span class="text-sm text-gray-700">{{ $clinic->contact_number ?? 'Contact not provided' }}</span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-200">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-600">Completed Appointments</span>
                                <span class="text-lg font-semibold text-gray-800">{{ $completedAppointmentsCount }}</span>
                            </div>
                            <a href="{{ route('clinic.appointments.archived') }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center mt-2">
                                <span>View appointment history</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Appointment Statistics Chart -->
                    <div class="lg:col-span-2 bg-white shadow-lg rounded-xl p-4 md:p-6 border border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-base md:text-lg font-semibold text-gray-800">Appointment Statistics</h3>
                            <div>
                                <select id="period-selector" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="yearly" {{ $period === 'yearly' ? 'selected' : '' }}>Yearly</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="relative" style="height:200px; min-height:200px; max-height:300px;">
                            <canvas id="appointmentChart"></canvas>
                        </div>
                        
                        <div class="mt-4 text-center text-sm text-gray-500">
                            <span id="chart-description">
                                @if($period === 'weekly')
                                    Last 7 days
                                @elseif($period === 'monthly')
                                    Last 30 days by week
                                @else
                                    Last 12 months
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                
                {{-- Notifications Section --}}
                <div class="mt-8 mb-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg md:text-xl font-semibold text-gray-800">Recent Notifications</h2>
                        @php
                            $unreadNotificationsCount = $clinic->notifications()->whereNull('read_at')->count();
                        @endphp
                        @if($unreadNotificationsCount > 0)
                            <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-blue-600 rounded-full">
                                {{ $unreadNotificationsCount }} unread
                            </span>
                        @endif
                    </div>
                    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
                        <div class="p-4">
                            @include('clinic.components.notifications', ['limit' => 5])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Initialize the appointment chart
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('appointmentChart').getContext('2d');
            
            // Get the data from the controller
            const labels = @json($appointmentStats['labels']);
            const data = @json($appointmentStats['data']);
            
            // Create the chart
            const appointmentChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Appointments',
                        data: data,
                        backgroundColor: 'rgba(37, 99, 235, 0.6)', // Changed to blue to match admin theme
                        borderColor: 'rgba(37, 99, 235, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#334155',
                            bodyColor: '#334155',
                            borderColor: '#e2e8f0',
                            borderWidth: 1,
                            padding: 10,
                            displayColors: false,
                            callbacks: {
                                title: function(tooltipItems) {
                                    return tooltipItems[0].label;
                                },
                                label: function(context) {
                                    return 'Appointments: ' + context.raw;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
            
            // Handle period changes
            document.getElementById('period-selector').addEventListener('change', function() {
                const period = this.value;
                
                // Update chart description
                const chartDescription = document.getElementById('chart-description');
                if (period === 'weekly') {
                    chartDescription.textContent = 'Last 7 days';
                } else if (period === 'monthly') {
                    chartDescription.textContent = 'Last 30 days by week';
                } else {
                    chartDescription.textContent = 'Last 12 months';
                }
                
                // Fetch new data
                fetch(`/clinic/dashboard/stats?period=${period}`)
                    .then(response => response.json())
                    .then(data => {
                        // Update chart data
                        appointmentChart.data.labels = data.labels;
                        appointmentChart.data.datasets[0].data = data.data;
                        appointmentChart.update();
                    })
                    .catch(error => console.error('Error fetching chart data:', error));
            });
        });
    </script>
</x-app-layout>
