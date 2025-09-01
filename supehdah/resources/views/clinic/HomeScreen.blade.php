<x-app-layout>
  <div class="py-12 bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex space-x-6">
      {{-- Sidebar (same as Dashboard) --}}
      <div class="w-1/4">
        @include('clinic.components.sidebar')
      </div>

      {{-- Main Content --}}
      <div class="w-3/4 space-y-8">
        {{-- Hero --}}
        <div class="relative overflow-hidden rounded-2xl shadow">
          <div class="w-full h-56 md:h-64 bg-gradient-to-r from-purple-500 to-pink-400">
            @if($homepage->hero_image)
              <img src="{{ asset('storage/' . $homepage->hero_image) }}" class="w-full h-full object-cover opacity-80" alt="Hero">
            @endif
          </div>
          <div class="absolute inset-0 flex items-center">
            <div class="px-6 md:px-10">
              <h1 class="text-white text-2xl md:text-4xl font-bold drop-shadow">
                {{ $homepage->hero_title ?: 'Welcome to ' . $clinic->clinic_name }}
              </h1>
              @if($homepage->hero_subtitle)
                <p class="text-white/90 mt-2 md:text-lg">{{ $homepage->hero_subtitle }}</p>
              @endif
            </div>
          </div>
        </div>

        {{-- Announcement --}}
        @if($homepage->announcement_title || $homepage->announcement_body || $homepage->announcement_image)
          <div class="bg-white rounded-xl shadow p-5 flex gap-4 items-start">
            @if($homepage->announcement_image)
              <img src="{{ asset('storage/' . $homepage->announcement_image) }}" class="w-24 h-24 rounded-lg object-cover" alt="Announcement">
            @else
              <div class="w-24 h-24 rounded-lg bg-pink-100 flex items-center justify-center">
                <span class="text-pink-500 font-bold text-xl">ANN</span>
              </div>
            @endif
            <div class="flex-1">
              <h3 class="text-purple-600 font-semibold text-lg">{{ $homepage->announcement_title ?? 'Announcement' }}</h3>
              <p class="text-gray-700 mt-1 whitespace-pre-line">{{ $homepage->announcement_body ?? 'No announcements yet.' }}</p>
            </div>
          </div>
        @endif

        {{-- About --}}
        <div class="bg-white rounded-xl shadow p-6">
          <h3 class="text-purple-600 font-semibold text-lg mb-2">About {{ $clinic->clinic_name }}</h3>
          <p class="text-gray-700 whitespace-pre-line">
            {{ $homepage->about_text ?? 'Tell your clients about your clinic, your mission, and what makes you special.' }}
          </p>
        </div>

        {{-- Services --}}
        <div>
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-purple-600 font-semibold text-lg">Services</h3>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($services as $svc)
              <div class="bg-white rounded-xl shadow overflow-hidden">
                @if($svc->image_path)
                  <img src="{{ asset('storage/' . $svc->image_path) }}" class="w-full h-40 object-cover" alt="{{ $svc->name }}">
                @endif
                <div class="p-4">
                  <div class="flex items-center justify-between">
                    <h4 class="font-semibold text-gray-900">{{ $svc->name }}</h4>
                    @if(!is_null($svc->price))
                      <span class="text-sm text-gray-600">₱{{ number_format($svc->price, 2) }}</span>
                    @endif
                  </div>
                  @if($svc->description)
                    <p class="text-sm text-gray-600 mt-1">{{ $svc->description }}</p>
                  @endif
                  @if(!$svc->is_active)
                    <span class="inline-block mt-2 text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-600">Inactive</span>
                  @endif
                </div>
              </div>
            @empty
              <p class="text-gray-600">No services yet. Add one below.</p>
            @endforelse
          </div>
        </div>

        {{-- Flash --}}
        @if(session('success'))
          <div class="bg-green-50 border border-green-200 text-green-700 rounded p-3">
            {{ session('success') }}
          </div>
        @endif

        {{-- Manage Content --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          {{-- Update Homepage Content --}}
          <form class="bg-white rounded-xl shadow p-5" method="POST" action="{{ route('clinic.home.update') }}" enctype="multipart/form-data">
            @csrf
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Update Homepage</h3>

            <label class="block text-sm text-gray-700 mb-1">Hero Title</label>
            <input type="text" name="hero_title" value="{{ old('hero_title', $homepage->hero_title) }}" class="w-full border rounded px-3 py-2 mb-3" required>

            <label class="block text-sm text-gray-700 mb-1">Hero Subtitle</label>
            <input type="text" name="hero_subtitle" value="{{ old('hero_subtitle', $homepage->hero_subtitle) }}" class="w-full border rounded px-3 py-2 mb-3">

            <label class="block text-sm text-gray-700 mb-1">Hero Image</label>
            <input type="file" name="hero_image" class="w-full border rounded px-3 py-2 mb-3">

            <label class="block text-sm text-gray-700 mb-1">About Text</label>
            <textarea name="about_text" rows="5" class="w-full border rounded px-3 py-2 mb-3">{{ old('about_text', $homepage->about_text) }}</textarea>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <div>
                <label class="block text-sm text-gray-700 mb-1">Announcement Title</label>
                <input type="text" name="announcement_title" value="{{ old('announcement_title', $homepage->announcement_title) }}" class="w-full border rounded px-3 py-2">
              </div>
              <div>
                <label class="block text-sm text-gray-700 mb-1">Announcement Image</label>
                <input type="file" name="announcement_image" class="w-full border rounded px-3 py-2">
              </div>
            </div>

            <label class="block text-sm text-gray-700 mt-3 mb-1">Announcement Body</label>
            <textarea name="announcement_body" rows="4" class="w-full border rounded px-3 py-2 mb-4">{{ old('announcement_body', $homepage->announcement_body) }}</textarea>

            <button class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">Save Homepage</button>

            @if($errors->any())
              <div class="mt-3 text-sm text-red-600">
                <ul class="list-disc ml-5">
                  @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                </ul>
              </div>
            @endif
          </form>

          {{-- Add Service --}}
          <form class="bg-white rounded-xl shadow p-5" method="POST" action="{{ route('clinic.services.store') }}" enctype="multipart/form-data">
            @csrf
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Add Service</h3>

            <label class="block text-sm text-gray-700 mb-1">Name</label>
            <input type="text" name="name" class="w-full border rounded px-3 py-2 mb-3" required>

            <label class="block text-sm text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="4" class="w-full border rounded px-3 py-2 mb-3"></textarea>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
              <div>
                <label class="block text-sm text-gray-700 mb-1">Price (₱)</label>
                <input type="number" step="0.01" name="price" class="w-full border rounded px-3 py-2">
              </div>
              <div>
                <label class="block text-sm text-gray-700 mb-1">Icon</label>
                <input type="text" name="icon" class="w-full border rounded px-3 py-2" placeholder="e.g. paw">
              </div>
              <div>
                <label class="block text-sm text-gray-700 mb-1">Order</label>
                <input type="number" name="order" class="w-full border rounded px-3 py-2" value="0">
              </div>
            </div>

            <label class="block text-sm text-gray-700 mt-3 mb-1">Image</label>
            <input type="file" name="image" class="w-full border rounded px-3 py-2 mb-4">

            <div class="flex items-center gap-2 mb-4">
              <input type="checkbox" name="is_active" value="1" checked>
              <span class="text-sm text-gray-700">Active</span>
            </div>

            <button class="px-4 py-2 bg-pink-500 text-white rounded hover:bg-pink-600">Add Service</button>
          </form>
        </div>

        {{-- Manage Services List --}}
        @if($services->count())
          <div class="bg-white rounded-xl shadow p-5">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Manage Services</h3>
            <div class="divide-y">
              @foreach($services as $svc)
                <div class="py-3 flex items-start justify-between gap-4">
                  <div>
                    <div class="font-semibold">{{ $svc->name }}</div>
                    @if(!is_null($svc->price))<div class="text-sm text-gray-600">₱{{ number_format($svc->price,2) }}</div>@endif
                    @if($svc->description)<div class="text-sm text-gray-600 mt-1">{{ $svc->description }}</div>@endif
                  </div>
                  <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('clinic.services.destroy', $svc) }}">
                      @csrf @method('DELETE')
                      <button class="px-3 py-1.5 text-red-600 border border-red-200 rounded hover:bg-red-50">Delete</button>
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