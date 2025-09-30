@php
    use App\Models\ClinicInfo;
    $clinic = $clinic ?? ClinicInfo::where('user_id', auth()->id())->first();
@endphp

<x-app-layout>
    {{-- Include mobile navigation (only visible on mobile) --}}
    @include('clinic.components.mobile-nav')

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:space-x-6">

                {{-- Sidebar (hidden on mobile) --}}
                <div class="hidden md:block md:w-1/4 mb-6 md:mb-0">
                    @include('clinic.components.sidebar')
                </div>

                {{-- Main Content --}}
                <div class="w-full md:w-3/4 mt-16 md:mt-0">
                <div class="bg-white shadow-xl rounded-lg p-4 sm:p-6 md:p-8">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-3 sm:mb-4">Manage Appointment Form Field</h2>

                    <div class="flex justify-end mb-4 sm:mb-6">
                    <a href="{{ route('appointments.preview', $clinic->id) }}" 
                        class="bg-green-500 hover:bg-green-600 text-white px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm rounded">
                                View Form Layout
                                </a>
                            </div>

                    @if(session('success'))
                        <div class="mb-4 text-green-600 text-sm">{{ session('success') }}</div>
                    @endif

                    {{-- Create Field Form --}}
                    <form action="{{ route('clinic.fields.store') }}" method="POST" class="space-y-3 sm:space-y-4 mb-6">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4">
                            <input type="text" name="label" placeholder="Field label (e.g. Pet)" value="{{ old('label') }}"
                                class="border rounded p-1.5 sm:p-2 text-sm" required>

                            <select name="type" id="field-type" class="border rounded p-1.5 sm:p-2 text-sm">
                                <option value="text">Text</option>
                                <option value="textarea">Textarea</option>
                                <option value="select">Dropdown (select)</option>
                                <option value="checkbox">Checkbox (multi)</option>
                                <option value="radio">Radio (single)</option>
                                <option value="date">Date</option>
                                <option value="time">Time</option>
                                <option value="number">Number</option>
                            </select>

                            <label class="flex items-center space-x-2 text-sm">
                                <input type="checkbox" name="required" value="1" class="form-checkbox">
                                <span>Required</span>
                            </label>
                        </div>

                        <div id="options-wrapper" class="hidden">
                            <label class="block text-xs sm:text-sm text-gray-700">Options (comma separated)
                                <small class="text-gray-500 block text-xs">Only for select/checkbox/radio — e.g. Dog, Cat, Rabbit</small>
                            </label>
                            <input type="text" name="options" value="{{ old('options') }}" class="border rounded p-1.5 sm:p-2 w-full text-sm">
                        </div>

                        <div>
                            <button class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm bg-green-600 text-white rounded hover:bg-green-700">Add Field</button>
                        </div>
                    </form>

                    {{-- Existing Fields Table --}}
                    <div class="overflow-x-auto -mx-4 sm:-mx-0">
                        <table class="min-w-full divide-y divide-gray-200 text-xs sm:text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 sm:px-4 py-1.5 sm:py-2 text-left text-xs font-medium text-gray-500">#</th>
                                    <th class="px-2 sm:px-4 py-1.5 sm:py-2 text-left text-xs font-medium text-gray-500">Label</th>
                                    <th class="px-2 sm:px-4 py-1.5 sm:py-2 text-left text-xs font-medium text-gray-500 hidden sm:table-cell">Type</th>
                                    <th class="px-2 sm:px-4 py-1.5 sm:py-2 text-left text-xs font-medium text-gray-500 hidden sm:table-cell">Required</th>
                                    <th class="px-2 sm:px-4 py-1.5 sm:py-2 text-left text-xs font-medium text-gray-500 hidden md:table-cell">Options</th>
                                    <th class="px-2 sm:px-4 py-1.5 sm:py-2 text-left text-xs font-medium text-gray-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($fields as $index => $f)
                                    <tr>
                                        <td class="px-2 sm:px-4 py-1.5 sm:py-2">{{ $index + 1 }}</td>
                                        <td class="px-2 sm:px-4 py-1.5 sm:py-2">
                                            {{ $f->label }}
                                            <span class="sm:hidden block text-xs text-gray-500">{{ ucfirst($f->type) }} {{ $f->required ? '(Required)' : '' }}</span>
                                        </td>
                                        <td class="px-2 sm:px-4 py-1.5 sm:py-2 hidden sm:table-cell">{{ ucfirst($f->type) }}</td>
                                        <td class="px-2 sm:px-4 py-1.5 sm:py-2 hidden sm:table-cell">{{ $f->required ? 'Yes' : 'No' }}</td>
                                        <td class="px-2 sm:px-4 py-1.5 sm:py-2 hidden md:table-cell">
                                            <span class="truncate max-w-[150px] block">{{ $f->options ? implode(', ', $f->options) : '-' }}</span>
                                        </td>
                                        <td class="px-2 sm:px-4 py-1.5 sm:py-2">
                                            <div class="flex flex-wrap gap-2 items-center">
                                                <a href="{{ route('clinic.fields.edit', $f->id) }}" class="text-indigo-600 hover:underline text-xs sm:text-sm">Edit</a>

                                                <form action="{{ route('clinic.fields.destroy', $f->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:underline text-xs sm:text-sm" onclick="return confirm('Delete this field?')">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-2 sm:px-4 py-3 sm:py-4 text-center text-gray-500 text-xs sm:text-sm">No fields added yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <script>
        // small JS to toggle options input
        const typeSelect = document.getElementById('field-type');
        const optionsWrapper = document.getElementById('options-wrapper');

        function toggleOptions() {
            const t = typeSelect.value;
            if (t === 'select' || t === 'checkbox' || t === 'radio') {
                optionsWrapper.classList.remove('hidden');
            } else {
                optionsWrapper.classList.add('hidden');
            }
        }

        if (typeSelect) {
            typeSelect.addEventListener('change', toggleOptions);
            document.addEventListener('DOMContentLoaded', toggleOptions);
        }
    </script>
</x-app-layout>
