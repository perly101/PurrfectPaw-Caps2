@php
    use App\Models\ClinicInfo;
    $clinic = $clinic ?? ClinicInfo::where('user_id', auth()->id())->first();
@endphp

<x-app-layout>
    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 flex space-x-6">
            <div class="w-1/4">
                @include('clinic.components.sidebar')
            </div>

            <div class="w-3/4">
                <div class="bg-white shadow-xl rounded-lg p-8">
                    <h2 class="text-xl font-bold mb-4">Edit Field</h2>

                    <form action="{{ route('clinic.fields.update', $field->id) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-sm font-medium">Label</label>
                            <input type="text" name="label" value="{{ old('label', $field->label) }}" class="border rounded p-2 w-full" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Type</label>
                            <select name="type" id="edit-type" class="border rounded p-2 w-full">
                                @php $types = ['text','textarea','select','checkbox','radio','date','time','number']; @endphp
                                @foreach($types as $t)
                                    <option value="{{ $t }}" {{ old('type', $field->type) === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="edit-options" class="{{ in_array($field->type, ['select','checkbox','radio']) ? '' : 'hidden' }}">
                            <label class="block text-sm font-medium">Options (comma separated)</label>
                            <input type="text" name="options" value="{{ old('options', $field->options ? implode(', ', $field->options) : '') }}" class="border rounded p-2 w-full">
                        </div>

                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="required" value="1" {{ $field->required ? 'checked' : '' }}>
                            <span>Required</span>
                        </label>

                        <div>
                            <button class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Update Field</button>
                            <a href="{{ route('clinic.fields.index') }}" class="ml-3 text-sm text-gray-600">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        const editType = document.getElementById('edit-type');
        const editOptions = document.getElementById('edit-options');

        function toggleEditOptions() {
            const t = editType.value;
            if (['select','checkbox','radio'].includes(t)) editOptions.classList.remove('hidden');
            else editOptions.classList.add('hidden');
        }

        if (editType) {
            editType.addEventListener('change', toggleEditOptions);
            document.addEventListener('DOMContentLoaded', toggleEditOptions);
        }
    </script>
</x-app-layout>
