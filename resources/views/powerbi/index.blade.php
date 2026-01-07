@extends('layouts.app')

@section('title', 'Power BI')

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 hover:opacity-80 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <h1 class="text-lg font-semibold">Power BI</h1>
        </a>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="toggleDarkMode()" class="p-2 rounded-lg hover:bg-white/10 transition" title="Alternar tema">
            <svg data-light-icon class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
            <svg data-dark-icon class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </button>
        <a href="{{ route('dashboard') }}" class="p-2 rounded-lg hover:bg-white/10 transition" title="Voltar ao Dashboard">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
        </a>
    </div>
@endsection

@section('content')
    <div class="h-full w-full bg-gray-100 dark:bg-gray-900">
        <iframe
            src="https://app.powerbi.com/view?r=eyJrIjoiZGNkNGQ3OTYtYjFkMi00OGZkLTljMmUtYjQ5YTg0ZTRhZTI3IiwidCI6IjVkNzdmY2E1LWIxZDEtNDI3OS1iNzk3LWEzYTY1NzA2Y2YxOSJ9"
            class="w-full h-full border-0"
            allowFullScreen="true">
        </iframe>
    </div>
@endsection
