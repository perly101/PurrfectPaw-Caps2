<x-app-layout>
    <div class="relative">
        @include('admin.components.sidebar')

        <div class="ml-64">
            <div class="py-6 px-4">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                    Step 2: Create Account
                </h2>

                <div class="bg-white shadow rounded-lg p-6">
                    @if(session('error'))
                        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('clinic.step2.submit') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" class="mt-1 block w-full border rounded p-2" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" name="password" class="mt-1 block w-full border rounded p-2" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="mt-1 block w-full border rounded p-2" required>
                        </div>

                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                            Register Clinic Account
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
