@extends('layouts.app')

@section('title', __('Entrar'))

@section('header')
    <div class="flex items-center gap-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <h1 class="text-lg font-semibold">POPRUA</h1>
    </div>
    <div></div>
@endsection

@section('content')
    <div class="h-full overflow-y-auto bg-gray-50 p-4">
        <div class="max-w-md mx-auto mt-8">
            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded-lg text-sm">
                    {{ __(session('status')) }}
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">{{ __('Entrar') }}</h2>

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email') }}</label>
                        <input id="email" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror" 
                               type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus 
                               autocomplete="username" />
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Password') }}</label>
                        <input id="password" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                               type="password"
                               name="password"
                               required 
                               autocomplete="current-password" />
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input id="remember_me" 
                               type="checkbox" 
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" 
                               name="remember">
                        <label for="remember_me" class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</label>
                    </div>

                    <div class="flex flex-col gap-3">
                        @if (Route::has('password.request'))
                            <a class="text-sm text-blue-600 hover:text-blue-800 underline" href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif

                        <button type="submit" class="w-full bg-blue-500 text-white py-2.5 rounded-lg font-medium hover:bg-blue-600 transition">
                            {{ __('Log in') }}
                        </button>

                        @if (Route::has('register'))
                            <div class="text-center text-sm text-gray-600">
                                {{ __('Não tem conta?') }} 
                                <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    {{ __('Register') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
