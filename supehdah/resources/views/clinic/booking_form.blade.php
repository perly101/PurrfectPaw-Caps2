@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg p-6">
    <h1 class="text-2xl font-bold mb-4">Book an Appointment - {{ $clinic->name }}</h1>

    <form action="{{ route('appointments.store', $clinic->id) }}" method="POST">
        @csrf

        {{-- Patient Name --}}
        <div class="mb-4">
            <label class="block text-gray-700">Patient Name</label>
            <input type="text" name="patient_name" class="w-full border rounded px-3 py-2">
        </div>

        {{-- Loop through Custom Fields --}}
        @foreach ($customFields as $field)
            <div class="mb-4">
                <label class="block text-gray-700">{{ $field->label }}</label>

                @if ($field->type === 'text')
                    <input type="text" name="custom_{{ $field->id }}" class="w-full border rounded px-3 py-2">

                @elseif ($field->type === 'textarea')
                    <textarea name="custom_{{ $field->id }}" class="w-full border rounded px-3 py-2"></textarea>

                @elseif ($field->type === 'select')
                    <select name="custom_{{ $field->id }}" class="w-full border rounded px-3 py-2">
                        @foreach (explode(',', $field->options) as $option)
                            <option value="{{ trim($option) }}">{{ trim($option) }}</option>
                        @endforeach
                    </select>

                @elseif ($field->type === 'date')
                    <input type="date" name="custom_{{ $field->id }}" class="w-full border rounded px-3 py-2">

                @elseif ($field->type === 'number')
                    <input type="number" name="custom_{{ $field->id }}" class="w-full border rounded px-3 py-2">
                @endif
            </div>
        @endforeach

        {{-- Submit Button --}}
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            Book Now
        </button>
    </form>
</div>
@endsection
