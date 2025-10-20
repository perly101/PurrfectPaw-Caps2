@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-semibold mb-6">Test Email Functionality</h1>
                
                @if (session('status'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
                        <strong class="font-bold">Success!</strong>
                        <span class="block sm:inline">{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('test-email.send') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                            Send test email to:
                        </label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            placeholder="Enter email address"
                            value="{{ old('email') }}"
                            required
                        >
                    </div>
                    
                    <div class="flex items-center">
                        <button
                            type="submit"
                            class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                        >
                            Send Test Email
                        </button>
                    </div>
                </form>
                
                <div class="mt-8 p-4 bg-gray-50 rounded">
                    <h2 class="text-lg font-medium mb-2">Email Configuration:</h2>
                    <ul class="list-disc pl-5 space-y-1 text-sm">
                        <li>MAIL_MAILER: <code>{{ config('mail.default') }}</code></li>
                        <li>MAIL_HOST: <code>{{ config('mail.mailers.' . config('mail.default') . '.host') }}</code></li>
                        <li>MAIL_FROM_ADDRESS: <code>{{ config('mail.from.address') }}</code></li>
                        <li>MAIL_FROM_NAME: <code>{{ config('mail.from.name') }}</code></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection