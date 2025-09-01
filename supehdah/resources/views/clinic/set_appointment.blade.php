@php
    use App\Models\ClinicInfo;
     use App\Models\CustomField;
    $clinic = ClinicInfo::where('user_id', auth()->id())->first();
    $petOptions = CustomField::where('clinic_id', $clinic->id)->where('type', 'pet')->pluck('value');
    $treatmentOptions = CustomField::where('clinic_id', $clinic->id)->where('type', 'treatment')->pluck('value');
@endphp

<x-app-layout>
    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex space-x-6">

            {{-- Sidebar --}}
            <div class="w-1/4">
                @include('clinic.components.sidebar')
            </div>

            {{-- Main Content --}}
            <div class="w-3/4">
                <div class="bg-white shadow-xl rounded-lg p-8">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Set Appointment Form</h2>

                    <form action="#" method="POST" class="space-y-5">
                        @csrf

                        {{-- Phone Number --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="text" name="phone_number" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                            {{-- Pet --}}
                             <div>
                        <label class="block text-sm font-medium text-gray-700">Pet</label>
                            <select name="pet" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            @foreach ($petOptions as $pet)
                             <option value="{{ $pet }}">{{ $pet }}</option>
                             @endforeach
                            </select>
                              </div>

                        {{-- Pet Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pet Name</label>
                            <input type="text" name="pet_name" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        {{-- Breed --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Breed</label>
                            <input type="text" name="breed" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                           {{-- Treatment --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Treatment</label>
        <select name="treatment" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @foreach ($treatmentOptions as $treatment)
                <option value="{{ $treatment }}">{{ $treatment }}</option>
            @endforeach
        </select>
    </div>


                        {{-- Date --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" name="date" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        {{-- Time --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Time</label>
                            <input type="time" name="time" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <small class="text-gray-500">Clinic may set gaps like 8:00 AM, 8:30, etc.</small>
                        </div>

                        <div>
                            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                                Save Appointment Fields
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
