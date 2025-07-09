@extends('layouts.app')

@section('title', 'Menunggu Verifikasi Vendor')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 max-w-lg text-center border-l-4 border-yellow-500">
        <div class="mb-4">
            <svg class="mx-auto h-12 w-12 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M12 8v.01M12 20c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8z" />
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-700 mb-2">Akun Anda Belum Terverifikasi</h2>
        <p class="text-gray-600 text-sm mb-4">
            Vendor Code Anda belum ditetapkan oleh admin. Silakan hubungi administrator untuk mengaktifkan akses dashboard.
        </p>
        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
            Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</div>
@endsection
