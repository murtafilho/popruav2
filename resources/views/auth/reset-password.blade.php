@extends('layouts.app')

@section('title', __('Redefinir Senha'))

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('login') }}" class="p-2 -ml-2 rounded-lg hover:bg-white/10 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-lg font-semibold flex-1 text-center">{{ __('Redefinir Senha') }}</h1>
        <div class="w-10"></div>
    </div>
@endsection

@section('content')
    <div class="h-full overflow-y-auto bg-gray-50 p-4">
        <div class="max-w-md mx-auto mt-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">{{ __('Redefinir Senha') }}</h2>

                <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-200 mb-1">{{ __('Email') }}</label>
                        <input id="email" 
                               class="w-full px-3 py-2 border border-gray-600 rounded-lg bg-[#1e2939] text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-[#1e2939] @error('email') border-red-500 @enderror" 
                               type="email" 
                               name="email" 
                               value="{{ old('email', $request->email) }}" 
                               required 
                               autofocus 
                               autocomplete="username" />
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-200 mb-1">{{ __('Password') }}</label>
                        <input id="password" 
                               class="w-full px-3 py-2 border border-gray-600 rounded-lg bg-[#1e2939] text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-[#1e2939] @error('password') border-red-500 @enderror" 
                               type="password" 
                               name="password" 
                               required 
                               autocomplete="new-password" />
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-200 mb-1">{{ __('Confirm Password') }}</label>
                        <input id="password_confirmation" 
                               class="w-full px-3 py-2 border border-gray-600 rounded-lg bg-[#1e2939] text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-[#1e2939] @error('password_confirmation') border-red-500 @enderror"
                               type="password"
                               name="password_confirmation" 
                               required 
                               autocomplete="new-password" />
                        @error('password_confirmation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-3">
                        <button type="submit" class="w-full bg-blue-500 text-white py-2.5 rounded-lg font-medium hover:bg-blue-600 transition">
                            {{ __('Reset Password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
