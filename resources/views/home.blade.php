@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="bg-white shadow-lg rounded-lg p-6 max-w-2xl mx-auto mt-10 text-center border-l-4 border-blue-500">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">
        Hello {{ auth()->user()->role === 'admin' ? 'Admin' : 'User' }}!
    </h1>
    <p class="text-lg text-gray-600">
        Welcome, <span class="font-semibold text-blue-600">{{ auth()->user()->name }}</span>.
    </p>
</div>
@endsection
