@extends('layouts.app')

@section('title', 'Perfil')

@section('header')
    <a href="{{ route('dashboard') }}" class="p-2 -ml-2 rounded-lg hover:bg-white/10 transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <h1 class="text-lg font-semibold flex-1 text-center">{{ __('Profile') }}</h1>
    <div class="w-10"></div>
@endsection

@section('content')
    <div class="h-full overflow-y-auto bg-gray-50 dark:bg-gray-900 p-4 space-y-4 transition-colors duration-200">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/50 p-4 transition-colors duration-200">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/50 p-4 transition-colors duration-200">
            @include('profile.partials.update-password-form')
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/50 p-4 transition-colors duration-200">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
@endsection
