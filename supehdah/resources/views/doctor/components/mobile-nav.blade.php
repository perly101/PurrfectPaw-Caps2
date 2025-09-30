<!-- Mobile Navigation Toggle -->
<div class="md:hidden fixed top-0 left-0 z-50 p-4">
    <button id="mobile-menu-button" class="text-gray-800 bg-white hover:bg-gray-100 p-2 rounded-lg shadow-md focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>
</div>

<!-- Mobile Sidebar (hidden by default) -->
<div id="mobile-sidebar" class="fixed inset-0 z-40 hidden">
    <div class="absolute inset-0 bg-gray-800 opacity-50" id="sidebar-backdrop"></div>
    <div class="absolute left-0 top-0 h-full w-64 bg-white shadow-xl transform transition-transform duration-300 -translate-x-full" id="sidebar-content">
        <div class="flex justify-end p-4">
            <button id="close-sidebar" class="text-gray-500 hover:text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        @include('doctor.components.sidebar')
    </div>
</div>

<!-- JavaScript for mobile sidebar -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileSidebar = document.getElementById('mobile-sidebar');
        const sidebarContent = document.getElementById('sidebar-content');
        const closeSidebar = document.getElementById('close-sidebar');
        const sidebarBackdrop = document.getElementById('sidebar-backdrop');
        
        if (mobileMenuButton && mobileSidebar && closeSidebar && sidebarBackdrop) {
            mobileMenuButton.addEventListener('click', function() {
                mobileSidebar.classList.remove('hidden');
                setTimeout(() => {
                    sidebarContent.classList.remove('-translate-x-full');
                }, 50);
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
</script>