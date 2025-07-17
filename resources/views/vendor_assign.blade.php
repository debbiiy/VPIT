@extends('layouts.app')

@section('content')
<div class="py-6 px-4">
<div class="p-6 bg-white shadow-lg rounded-lg border-l-4 border-blue-500">
    <h2 class="text-xl font-bold mb-4">Assign Vendor Code</h2>
    @if(session('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('assign.vendor') }}" class="mb-6">
        @csrf
        <div class="mb-4">
            <label>User (Tanpa Vendor Code)</label>
            <select name="user_id" class="border p-3 border-gray-300 w-full">
                <option value="" disabled selected>Pilih User</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->email }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label>Vendor Code</label>
            <select name="vendor_code" class="border p-3 border-gray-300 w-full">
                <option value="" disabled selected>Pilih Vendor Code</option>
                @foreach($vendorList as $vendor)
                    <option value="{{ $vendor->vendor_code }}">{{ $vendor->vendor_code }} - {{ $vendor->vendor_name }}</option>
                @endforeach
            </select>
        </div>

        <button class="bg-blue-600 text-white px-4 py-2 rounded">Assign</button>
    </form>

    <h3 class="text-lg font-semibold mb-2">Tambah Vendor Baru</h3>
    <form method="POST" action="{{ route('store.vendor') }}">
        @csrf
        <input type="text" name="vendor_code" placeholder="Kode Vendor" class="border-gray-300 p-3 w-full mb-2" />
        <input type="text" name="vendor_name" placeholder="Nama Vendor" class="border-gray-300 p-3 w-full mb-2" />
        <button class="bg-green-600 text-white px-4 py-2 rounded">Tambah Vendor</button>
    </form>
</div>
@endsection
