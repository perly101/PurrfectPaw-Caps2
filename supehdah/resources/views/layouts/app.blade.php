<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    
    <!-- Custom Responsive CSS -->
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Notifications Script (clinic only) -->
    @if(auth()->check() && auth()->user()->role === 'clinic')
        <script src="{{ asset('js/enhanced-notification-sound.js') }}"></script>
        <script src="{{ asset('js/global-notifications.js') }}"></script>
        <script src="{{ asset('js/notifications.js') }}"></script>
    @endif
    
    <!-- Additional Responsive Styles -->
    <style>
        @media (max-width: 768px) {
            .md\:hidden-custom {
                display: none !important;
            }
            .md\:block-custom {
                display: block !important;
            }
            .md\:flex-col-custom {
                flex-direction: column !important;
            }
        }
        @media (max-width: 640px) {
            .sm\:px-4-custom {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
            .sm\:text-sm-custom {
                font-size: 0.875rem !important;
            }
        }
    </style>
</head>
<body class="font-sans antialiased">
    <!-- Global Notification Container (only for clinic users) -->
    @if(auth()->check() && auth()->user()->role === 'clinic')
        <div id="global-notification-container" class="fixed top-0 right-0 z-50 p-4 w-full md:w-80 pointer-events-none"></div>
        <div id="global-popup-notification-container" class="fixed bottom-4 right-4 z-50 pointer-events-none"></div>
        <audio id="global-notification-sound" src="{{ asset('sounds/noti.mp3') }}" preload="auto" style="display: none;"></audio>
    @endif

    <div class="min-h-screen bg-gray-100">

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            @hasSection('content')
                @yield('content')
            @else
                @isset($slot)
                    {{ $slot }}
                @endisset
            @endif
        </main>
    </div>

    @if(session('success'))
    <script>
        Swal.fire({
            title: 'Success!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonText: 'OK'
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        Swal.fire({
            title: 'Error!',
            text: "{{ session('error') }}",
            icon: 'error',
            confirmButtonText: 'OK'
        });
    </script>
    @endif
</body>
</html>
