@php
    use App\Models\ClinicInfo;
    $clinic = $clinic ?? ClinicInfo::where('user_id', auth()->id())->first();
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
                    <h2 class="text-xl font-bold text-gray-800 mb-4">
                        Preview Booking Form - {{ $clinic->clinic_name }}
                    </h2>
                    <p class="text-gray-500 mb-6">
                        Try filling this out as if you were booking an appointment.
                    </p>

                    <form>
                        {{-- Patient Name --}}
                        <div class="mb-4">
                            <label class="block text-gray-700">Patient Name</label>
                            <input type="text" class="w-full border rounded px-3 py-2">
                        </div>

                        {{-- Loop through Custom Fields --}}
                        @foreach ($customFields as $field)
                            @php
                                $options = is_array($field->options) ? $field->options : json_decode($field->options, true);
                            @endphp

                            <div class="mb-4">
                                <label class="block text-gray-700">
                                    {{ $field->label }}
                                    @if($field->required) <span class="text-red-500">*</span> @endif
                                </label>

                                @if ($field->type === 'text')
                                    <input type="text" class="w-full border rounded px-3 py-2">

                                @elseif ($field->type === 'textarea')
                                    <textarea class="w-full border rounded px-3 py-2"></textarea>

                                @elseif ($field->type === 'select')
                                    <select class="w-full border rounded px-3 py-2">
                                        <option value="">Select an option</option>
                                        @foreach ($options ?? [] as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>

                                @elseif ($field->type === 'date')
                                    <input type="date" class="w-full border rounded px-3 py-2">

                                @elseif ($field->type === 'time')
                                    <input type="time" class="w-full border rounded px-3 py-2">

                                @elseif ($field->type === 'number')
                                    <input type="number" class="w-full border rounded px-3 py-2">
                                @endif
                            </div>
                        @endforeach
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
