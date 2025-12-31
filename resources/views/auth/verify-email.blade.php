@extends('layouts.app')

@section('title', __('Verificar Email'))

@section('header')
    <div class="flex items-center gap-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        <h1 class="text-lg font-semibold">{{ __('Verificar Email') }}</h1>
    </div>
    <form method="POST" action="{{ route('logout') }}" class="inline">
        @csrf
        <button type="submit" class="text-sm underline hover:no-underline">
            {{ __('Log Out') }}
        </button>
    </form>
@endsection

@section('content')
    <div class="h-full overflow-y-auto bg-gray-50 p-4">
        <div class="max-w-md mx-auto mt-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Verificar Email') }}</h2>
                
                <p class="mb-4 text-sm text-gray-600">
                    {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                </p>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded-lg text-sm">
                        {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('verification.send') }}" class="space-y-4">
                    @csrf
                    <button type="submit" class="w-full bg-blue-500 text-white py-2.5 rounded-lg font-medium hover:bg-blue-600 transition">
                        {{ __('Resend Verification Email') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
