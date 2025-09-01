{{-- resources/views/admin/clinic/create_step1.blade.php --}}

<x-app-layout>
    <div class="relative">
        {{-- Sidebar --}}
        @include('admin.components.sidebar')

        {{-- Main Content --}}
        <div class="ml-64">
            <div class="py-6 px-4">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                    {{ __('Step 1: Clinic Information') }}
                </h2>

                <div class="bg-white shadow rounded-lg p-6 max-w-2xl">
                    <form method="POST" action="{{ route('clinic.step1.store') }}">
                        @csrf

                        {{-- Clinic Name --}}
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                                Clinic Name
                            </label>
                            <input id="name" name="name" type="text" required
                                   class="shadow-sm border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Enter clinic name">
                        </div>

                        {{-- Clinic Address --}}
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="address">
                                Address
                            </label>
                            <input id="address" name="address" type="text" required
                                   class="shadow-sm border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Enter address">
                        </div>

                        {{-- Contact Number --}}
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="contact_number">
                                Contact Number
                            </label>
                            <input id="contact_number" name="contact_number" type="text" required
                                   class="shadow-sm border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Enter contact number">
                        </div>

                        {{-- Logo (Optional) --}}
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="logo">
                                Logo (URL or Upload later)
                            </label>
                            <input id="logo" name="logo" type="text"
                                   class="shadow-sm border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Enter logo URL (optional)">
                        </div>

                        <div class="mt-6">
                            <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Next: Create Account →
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
