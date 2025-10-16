@php
    use App\Models\ClinicInfo;
    $clinic = ClinicInfo::where('user_id', auth()->id())->first();
@endphp

<x-app-layout>
    {{-- Include mobile navigation (only visible on mobile) --}}
    @include('clinic.components.mobile-nav')

    <div class="py-6 md:py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row md:space-x-6">

            {{-- Sidebar (hidden on mobile) --}}
            <div class="hidden md:block md:w-1/4 lg:w-1/5">
                @include('clinic.components.sidebar')
            </div>

            {{-- Main Dashboard Content --}}
            <div class="w-full md:w-3/4 lg:w-4/5 mt-16 md:mt-0">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Dashboard</h1>
                    <p class="text-gray-600">Welcome back{{ $clinic ? ', ' . $clinic->clinic_name : '' }}!</p>
                </div>
                
                {{-- Stats Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <!-- Doctor Count -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 rounded-full bg-blue-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Doctors</p>
                                <p class="text-xl font-semibold text-gray-700">{{ $doctorCount }}</p>
                            </div>
                        </div>
                        <a href="{{ route('clinic.doctors.index') }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                            <span>View all doctors</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                    <!-- Patients Count -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 rounded-full bg-green-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Patients</p>
                                <p class="text-xl font-semibold text-gray-700">{{ $patientCount }}</p>
                            </div>
                        </div>
                        <a href="{{ route('clinic.patients.index') }}" class="text-sm text-green-600 hover:text-green-800 flex items-center">
                            <span>View all patients</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                    
                    <!-- Pending Appointments -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 rounded-full bg-amber-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Pending Appointments</p>
                                <p class="text-xl font-semibold text-gray-700">{{ $pendingAppointmentsCount }}</p>
                            </div>
                        </div>
                        <a href="{{ route('clinic.appointments.index') }}" class="text-sm text-amber-600 hover:text-amber-800 flex items-center">
                            <span>View pending appointments</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                    
                    <!-- Today's Appointments -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 rounded-full bg-purple-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Today's Appointments</p>
                                <p class="text-xl font-semibold text-gray-700">{{ $todayAppointmentsCount }}</p>
                            </div>
                        </div>
                        <a href="{{ route('clinic.appointments.index') }}" class="text-sm text-purple-600 hover:text-purple-800 flex items-center">
                            <span>View today's schedule</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Clinic Info & Stats -->
                    <div class="bg-white shadow-xl rounded-lg p-4 md:p-6">
                        @if ($clinic && $clinic->profile_picture)
                            <img src="{{ asset('storage/' . $clinic->profile_picture) }}"
                                class="w-24 h-24 rounded-full object-cover shadow mx-auto mb-4 border-4 border-indigo-200"
                                alt="Clinic Profile Picture">
                        @else
                            <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 mx-auto mb-4">
                                <span class="text-sm">No Image</span>
                            </div>
                        @endif

                        <div class="mt-4">
                            <h3 class="text-xl font-semibold text-gray-800 text-center mb-4">
                                {{ $clinic->clinic_name ?? 'Your Clinic' }}
                            </h3>

                            <div class="space-y-3">
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
                                
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm text-gray-700">{{ $clinic->is_open ? 'Currently Open' : 'Currently Closed' }}</span>
                                </div>
                            </div>

                            <div class="mt-6 pt-4 border-t border-gray-100">
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-600">Completed Appointments</span>
                                    <span class="text-sm font-semibold text-gray-800">{{ $completedAppointmentsCount }}</span>
                                </div>
                                <a href="{{ route('clinic.appointments.archived') }}" class="text-sm text-indigo-600 hover:text-indigo-800 flex items-center mt-2">
                                    <span>View appointment history</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Appointment Statistics Chart -->
                    <div class="lg:col-span-2 bg-white shadow-xl rounded-lg p-4 md:p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Appointment Statistics</h3>
                            <div>
                                <select id="period-selector" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="yearly" {{ $period === 'yearly' ? 'selected' : '' }}>Yearly</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="relative h-80">
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
                
                <!-- Notifications Section -->
                <div class="mt-8 bg-white shadow-xl rounded-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 py-4 px-6 flex justify-between items-center">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                            </svg>
                            <h3 class="ml-2 text-white font-semibold">Recent Notifications</h3>
                        </div>
                        @php
                            $unreadNotificationsCount = $clinic->notifications()->whereNull('read_at')->count();
                        @endphp
                        @if($unreadNotificationsCount > 0)
                            <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-indigo-100 bg-white/20 rounded-full">
                                {{ $unreadNotificationsCount }} unread
                            </span>
                        @endif
                    </div>
                    <div class="p-4">
                        @include('clinic.components.notifications', ['limit' => 5])
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
                        backgroundColor: 'rgba(79, 70, 229, 0.6)',
                        borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
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
