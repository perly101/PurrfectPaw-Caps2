<x-app-layout>
  {{-- Include mobile navigation (only visible on mobile) --}}
  @include('clinic.components.mobile-nav')

  <div class="py-12 bg-gray-100 min-h-screen">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col md:flex-row md:space-x-6">
        {{-- Sidebar (hidden on mobile) --}}
        <div class="hidden md:block md:w-1/6 mb-6 md:mb-0">
          @include('clinic.components.sidebar')
        </div>

        {{-- Main Content --}}
        <div class="w-full md:w-5/6 space-y-6 mt-16 md:mt-0">
        {{-- Hero --}}
        <div class="relative overflow-hidden rounded-lg shadow-md">
          <div class="w-full h-40 sm:h-48 md:h-56 lg:h-64 bg-gradient-to-r from-purple-500 to-pink-400">
            @if($homepage->hero_image)
              <img src="{{ asset('storage/' . $homepage->hero_image) }}" class="w-full h-full object-cover opacity-80" alt="Hero">
            @endif
          </div>
          <div class="absolute inset-0 flex items-center">
            <div class="px-4 sm:px-6 md:px-8 lg:px-10 w-full">
              <h1 class="text-white text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold drop-shadow-lg">
                {{ $homepage->hero_title ?: 'Welcome to ' . $clinic->clinic_name }}
              </h1>
              @if($homepage->hero_subtitle)
                <p class="text-white/90 mt-1 sm:mt-2 text-sm sm:text-base md:text-lg max-w-3xl">{{ $homepage->hero_subtitle }}</p>
              @endif
            </div>
          </div>
        </div>

        {{-- Announcement --}}
        @if($homepage->announcement_title || $homepage->announcement_body || $homepage->announcement_image)
          <div class="bg-white rounded-lg shadow-md p-5 flex flex-col sm:flex-row gap-4 sm:gap-5 items-center sm:items-start">
            @if($homepage->announcement_image)
              <img src="{{ asset('storage/' . $homepage->announcement_image) }}" class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 rounded-lg object-cover" alt="Announcement">
            @else
              <div class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 rounded-lg bg-pink-100 flex items-center justify-center">
                <span class="text-pink-500 font-bold text-base sm:text-lg md:text-xl">ANN</span>
              </div>
            @endif
            <div class="flex-1 text-center sm:text-left">
              <h3 class="text-purple-600 font-semibold text-base sm:text-lg">{{ $homepage->announcement_title ?? 'Announcement' }}</h3>
              <p class="text-gray-700 mt-2 whitespace-pre-line text-sm sm:text-base">{{ $homepage->announcement_body ?? 'No announcements yet.' }}</p>
            </div>
          </div>
        @endif

        {{-- About --}}
        <div class="bg-white rounded-lg shadow-md p-5 sm:p-6">
          <h3 class="text-purple-600 font-semibold text-base sm:text-lg mb-3">About {{ $clinic->clinic_name }}</h3>
          <p class="text-gray-700 whitespace-pre-line text-sm sm:text-base leading-relaxed">
            {{ $homepage->about_text ?? 'Tell your clients about your clinic, your mission, and what makes you special.' }}
          </p>
        </div>

        {{-- Services --}}
        <div>
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-purple-600 font-semibold text-base sm:text-lg">Services</h3>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
            @forelse($services as $svc)
              <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                @if($svc->image_path)
                  <img src="{{ asset('storage/' . $svc->image_path) }}" class="w-full h-32 sm:h-36 object-cover" alt="{{ $svc->name }}">
                @endif
                <div class="p-4">
                  <div class="flex flex-wrap items-center justify-between gap-2">
                    <h4 class="font-semibold text-gray-900 text-sm sm:text-base">{{ $svc->name }}</h4>
                    @if(!is_null($svc->price))
                      <span class="text-xs sm:text-sm text-gray-600 font-medium">₱{{ number_format($svc->price, 2) }}</span>
                    @endif
                  </div>
                  @if($svc->description)
                    <p class="text-xs sm:text-sm text-gray-600 mt-2">{{ $svc->description }}</p>
                  @endif
                  @if(!$svc->is_active)
                    <span class="inline-block mt-2 text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-600">Inactive</span>
                  @endif
                </div>
              </div>
            @empty
              <p class="text-gray-600 text-sm">No services yet. Add one below.</p>
            @endforelse
          </div>
        </div>

        {{-- Flash --}}
        @if(session('success'))
          <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 text-sm sm:text-base rounded">
            {{ session('success') }}
          </div>
        @endif

        {{-- Manage Content --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          {{-- Update Homepage Content --}}
          <form class="bg-white rounded-lg shadow-md p-5" method="POST" action="{{ route('clinic.home.update') }}" enctype="multipart/form-data">
            @csrf
            <h3 class="text-base font-semibold text-gray-800 mb-3">Update Homepage</h3>

            <label class="block text-xs sm:text-sm text-gray-700 mb-1">Hero Title</label>
            <input type="text" name="hero_title" value="{{ old('hero_title', $homepage->hero_title) }}" class="w-full border rounded px-2 sm:px-3 py-1.5 sm:py-2 mb-3 text-sm" required>

            <label class="block text-xs sm:text-sm text-gray-700 mb-1">Hero Subtitle</label>
            <input type="text" name="hero_subtitle" value="{{ old('hero_subtitle', $homepage->hero_subtitle) }}" class="w-full border rounded px-2 sm:px-3 py-1.5 sm:py-2 mb-3 text-sm">

            <label class="block text-xs sm:text-sm text-gray-700 mb-1">Hero Image</label>
            <input type="file" name="hero_image" class="w-full border rounded px-2 sm:px-3 py-1.5 sm:py-2 mb-3 text-sm">

            <label class="block text-xs sm:text-sm text-gray-700 mb-1">About Text</label>
            <textarea name="about_text" rows="4" class="w-full border rounded px-2 sm:px-3 py-1.5 sm:py-2 mb-3 text-sm">{{ old('about_text', $homepage->about_text) }}</textarea>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-xs sm:text-sm text-gray-700 mb-1">Announcement Title</label>
                <input type="text" name="announcement_title" value="{{ old('announcement_title', $homepage->announcement_title) }}" class="w-full border rounded px-2 sm:px-3 py-1.5 sm:py-2 text-sm">
              </div>
              <div>
                <label class="block text-xs sm:text-sm text-gray-700 mb-1">Announcement Image</label>
                <input type="file" name="announcement_image" class="w-full border rounded px-2 sm:px-3 py-1.5 sm:py-2 text-sm">
              </div>
            </div>

            <label class="block text-xs sm:text-sm text-gray-700 mt-3 mb-1">Announcement Body</label>
            <textarea name="announcement_body" rows="3" class="w-full border rounded px-2 sm:px-3 py-1.5 sm:py-2 mb-4 text-sm">{{ old('announcement_body', $homepage->announcement_body) }}</textarea>

            <button class="px-3 sm:px-4 py-1.5 sm:py-2 bg-purple-600 text-white rounded hover:bg-purple-700 text-xs sm:text-sm">Save Homepage</button>

            @if($errors->any())
              <div class="mt-3 text-xs sm:text-sm text-red-600">
                <ul class="list-disc ml-5">
                  @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                </ul>
              </div>
            @endif
          </form>

          {{-- Add Service --}}
          <form class="bg-white rounded-lg shadow-md p-5" method="POST" action="{{ route('clinic.services.store') }}" enctype="multipart/form-data">
            @csrf
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4">Add Service</h3>

            <label class="block text-xs sm:text-sm text-gray-700 mb-1.5">Name</label>
            <input type="text" name="name" class="w-full border rounded px-3 sm:px-4 py-2 sm:py-2.5 mb-4 text-sm" required>

            <label class="block text-xs sm:text-sm text-gray-700 mb-1.5">Description</label>
            <textarea name="description" rows="3" class="w-full border rounded px-3 sm:px-4 py-2 sm:py-2.5 mb-4 text-sm"></textarea>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
              <div>
                <label class="block text-xs sm:text-sm text-gray-700 mb-1.5">Price (₱)</label>
                <input type="number" step="0.01" name="price" class="w-full border rounded px-3 sm:px-4 py-2 sm:py-2.5 text-sm">
              </div>
              <div>
                <label class="block text-xs sm:text-sm text-gray-700 mb-1.5">Icon</label>
                <input type="text" name="icon" class="w-full border rounded px-3 sm:px-4 py-2 sm:py-2.5 text-sm" placeholder="e.g. paw">
              </div>
              <div>
                <label class="block text-xs sm:text-sm text-gray-700 mb-1.5">Order</label>
                <input type="number" name="order" class="w-full border rounded px-3 sm:px-4 py-2 sm:py-2.5 text-sm" value="0">
              </div>
            </div>

            <label class="block text-xs sm:text-sm text-gray-700 mt-4 mb-1.5">Image</label>
            <input type="file" name="image" class="w-full border rounded px-3 sm:px-4 py-2 sm:py-2.5 mb-4 text-sm">

            <div class="flex items-center gap-2.5 mb-5">
              <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4">
              <span class="text-xs sm:text-sm text-gray-700">Active</span>
            </div>

            <button class="px-4 sm:px-5 py-2 sm:py-2.5 bg-pink-500 text-white rounded hover:bg-pink-600 text-xs sm:text-sm transition-colors">Add Service</button>
          </form>
        </div>

        {{-- Manage Services List --}}
        @if($services->count())
          <div class="bg-white rounded-lg shadow-md p-5 sm:p-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4">Manage Services</h3>
            <div class="divide-y">
              @foreach($services as $svc)
                <div class="py-3 sm:py-4 flex flex-col sm:flex-row sm:items-start justify-between gap-3 sm:gap-5">
                  <div class="w-full sm:w-auto">
                    <div class="font-semibold text-sm sm:text-base">{{ $svc->name }}</div>
                    @if(!is_null($svc->price))<div class="text-xs sm:text-sm text-gray-600 mt-0.5">₱{{ number_format($svc->price,2) }}</div>@endif
                    @if($svc->description)<div class="text-xs sm:text-sm text-gray-600 mt-1.5">{{ $svc->description }}</div>@endif
                  </div>
                  <div class="flex items-center gap-3 mt-2 sm:mt-0">
                    <form method="POST" action="{{ route('clinic.services.destroy', $svc) }}">
                      @csrf @method('DELETE')
                      <button class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-red-600 border border-red-200 rounded hover:bg-red-50 transition-colors">Delete</button>
                    </form>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
</x-app-layout>