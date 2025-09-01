@php
    use App\Models\ClinicInfo;
    $clinic = ClinicInfo::where('user_id', auth()->id())->first();
@endphp

<x-app-layout>
    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex space-x-6">

            {{-- Sidebar --}}
             <div class="w-1/4">
                @include('clinic.components.sidebar')
            </div>

            {{-- Gallery Content --}}
            <div class="flex-1">
                <div class="mb-6">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-gray-800">Clinic Gallery</h2>
                        <div class="text-sm text-gray-500">{{ $images->count() }} photos</div>
                    </div>
                    <p class="text-gray-500 mt-1">Upload and manage your clinic's photos to showcase your facilities</p>
                </div>

                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-sm">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="font-medium">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Upload Section -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Upload New Photo</h3>
                    
                    <form action="{{ route('clinic.gallery.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="border-2 border-dashed border-gray-300 rounded-lg px-6 py-10 text-center hover:bg-gray-50 transition-colors">
                            <div class="space-y-2">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex justify-center text-sm text-gray-600">
                                    <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                        <span>Upload an image</span>
                                        <input id="file-upload" name="image" type="file" class="sr-only" onchange="updateFileName(this)">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 5MB</p>
                                <p id="file-name" class="text-sm text-gray-500 mt-2 hidden"></p>
                            </div>
                        </div>
                        
                        @error('image')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                        
                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                Upload Photo
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Gallery Section -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Photo Gallery</h3>
                    </div>

                    @if($images->count())
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                            @foreach($images as $img)
                                <div class="group relative rounded-lg overflow-hidden shadow-md bg-white hover:shadow-xl transition-all duration-300">
                                    <!-- Image with aspect ratio -->
                                    <div class="relative pb-[75%]">
                                        <img 
                                            src="{{ asset('storage/' . $img->image_path) }}" 
                                            class="absolute inset-0 w-full h-full object-cover"
                                            alt="Gallery image"
                                            loading="lazy"
                                            onclick="openImagePreview('{{ asset('storage/' . $img->image_path) }}')"
                                        >
                                    </div>
                                    
                                    <!-- Overlay with actions -->
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 flex items-center justify-center transition-all duration-300 opacity-0 group-hover:opacity-100">
                                        <div class="flex space-x-2">
                                            <button 
                                                type="button" 
                                                class="p-2 bg-white rounded-full shadow-lg hover:bg-gray-100 focus:outline-none"
                                                onclick="openImagePreview('{{ asset('storage/' . $img->image_path) }}')"
                                            >
                                                <svg class="h-5 w-5 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <form action="{{ route('clinic.gallery.delete', $img->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this image?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 bg-white rounded-full shadow-lg hover:bg-gray-100 focus:outline-none">
                                                    <svg class="h-5 w-5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-lg py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No photos</h3>
                            <p class="mt-1 text-sm text-gray-500">Upload photos to showcase your clinic facilities and services.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div id="imagePreviewModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-75">
        <div class="flex items-center justify-center h-full">
            <div class="relative max-w-4xl w-full p-4">
                <button onclick="closeImagePreview()" class="absolute top-0 right-0 -mt-10 -mr-6 text-white text-3xl font-bold hover:text-gray-300">&times;</button>
                <img id="previewImage" src="" class="w-full h-auto max-h-[80vh] object-contain rounded-lg" alt="Preview">
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function updateFileName(input) {
            const fileNameElement = document.getElementById('file-name');
            if (input.files && input.files[0]) {
                fileNameElement.textContent = 'Selected: ' + input.files[0].name;
                fileNameElement.classList.remove('hidden');
            } else {
                fileNameElement.classList.add('hidden');
            }
        }

        function openImagePreview(imageUrl) {
            const modal = document.getElementById('imagePreviewModal');
            const previewImage = document.getElementById('previewImage');
            
            previewImage.src = imageUrl;
            modal.classList.remove('hidden');
            
            // Prevent scrolling on the body
            document.body.style.overflow = 'hidden';
        }

        function closeImagePreview() {
            const modal = document.getElementById('imagePreviewModal');
            modal.classList.add('hidden');
            
            // Re-enable scrolling
            document.body.style.overflow = 'auto';
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeImagePreview();
            }
        });
    </script>
</x-app-layout>
