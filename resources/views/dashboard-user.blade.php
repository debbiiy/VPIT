@extends('layouts.app')

@section('title', 'User Dashboard')

@section('content')
@if(session('error'))
    <div class="bg-red-100 text-red-700 px-4 py-2 rounded border border-red-400 mb-4">
        {{ session('error') }}
    </div>
@endif

<div class="py-6">
    <div class="w-full px-4">
        <div class="bg-white shadow-xl rounded-lg p-6 w-full border-l-4 border-blue-500">
            <h3 class="text-lg font-medium text-gray-700 mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-5 h-5 mr-2 text-gray-800" viewBox="0 0 24 24">
                    <path d="M16.862 3.487l3.651 3.651a1.25 1.25 0 010 1.768l-9.546 9.546a1.25 1.25 0 01-.547.317l-4.65 1.244a.5.5 0 01-.617-.617l1.244-4.65a1.25 1.25 0 01.317-.547l9.546-9.546a1.25 1.25 0 011.768 0zM5 20.75a.75.75 0 000 1.5h14a.75.75 0 000-1.5H5z"/>
                </svg>
                <b> Job Order </b>
            </h3>

            <!-- Filter -->
            <form method="GET" action="{{ route('user.dashboard') }}" class="mb-4 flex items-center gap-2">
              <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search..." 
                    class="border border-gray-300 rounded-md px-4 py-2 text-base w-72 focus:outline-none focus:ring-2 focus:ring-blue-400"
                >
                <button 
                    type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md text-base"
                >
                    Search
                </button>
            </form>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto text-sm text-left border border-gray-300">
                    <thead class="border border-gray-300">
                        <tr class="bg-gray-100 text-gray-700">
                            <th class="p-2 border">No</th>
                            <th class="p-2 border">Jo No</th>
                            <th class="p-2 border">Shipper</th>
                            <th class="p-2 border">Vendor</th>
                            <th class="p-2 border">Location</th>
                            <th class="p-2 border">Price</th>
                            <th class="p-2 border">Container</th>
                            <th class="p-2 border">Ctr Code</th>
                            <th class="p-2 border">Truck</th>
                            <th class="p-2 border">Stuffing</th>
                            <th class="p-2 border">File</th>
                            <th class="p-2 border">Status</th>
                            <th class="p-2 border">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $row = 1; $printed = []; @endphp
                        @forelse($files as $file)

                            @foreach($file->jobCost as $cost)
                                @php
                                    $key = $file->jo_code . '-' . $cost->container_no . '-' . $cost->truck_no;
                                @endphp
                                @if(!in_array($key, $printed))
                                    @php
                                        $printed[] = $key;
                                        $docMatch = \App\Models\VpitDoc::where('jo_code', $file->jo_code)
                                            ->where('container_no', $cost->container_no)
                                            ->first();
                                    @endphp
                                    <tr class="border border-gray-300 hover:bg-gray-50">
                                        <td class="p-2 border">{{ $row++ }}</td>
                                        <td class="p-2 border">{{ $file->jobOrder->code_manual ?? '-' }}</td>
                                        <td class="p-2 border">{{ $file->jobOrder->shipper_name ?? '-' }}</td>
                                        <td class="p-2 border">{{ $cost->vendor ?? '-' }}</td>
                                        <td class="p-2 border">{{ $cost->location ?? '-' }}</td>
                                        <td class="p-2 border">Rp {{ number_format($cost->amount ?? 0) }}</td>
                                        <td class="p-2 border">{{ $cost->container_no ?? '-' }}</td>
                                        <td class="p-2 border">{{ $cost->container_size ?? '-' }}</td>
                                        <td class="p-2 border">{{ $cost->truck_no ?? '-' }}</td>
                                        <td class="p-2 border">{{ $cost->stuffing_place ?? '-' }}</td>
                                        <td class="p-2 border text-center">
                                            @if($docMatch && $docMatch->file)
                                                <a href="{{ asset('storage/files/' . $docMatch->file) }}" target="_blank">
                                                    <i class="fa-regular fa-eye text-black-500 hover:text-gray-800 text-base"></i>
                                                </a>
                                            @else
                                                <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Empty</span>
                                            @endif
                                        </td>
                                        <td class="p-2 border text-center">
                                            @if($docMatch)
                                                <span class="px-2 py-1 text-xs rounded {{ $docMatch->is_status == 1 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ $docMatch->is_status == 1 ? 'Approved' : 'Waiting' }}
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs rounded bg-gray-200 text-gray-600">Not Uploaded</span>
                                            @endif
                                        </td>
                                        <td class="p-2 border text-center">
                                            @if(!$docMatch || !$docMatch->file)
                                                <button onclick="document.getElementById('uploadModal-{{ $file->id }}-{{ $cost->container_no }}').showModal()" title="Upload Surat Jalan">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-600 hover:text-blue-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5-5m0 0l5 5m-5-5v12" />
                                                    </svg>
                                                </button>
                                            @else
                                                <span class="text-gray-400 text-sm">Uploaded</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <dialog id="uploadModal-{{ $file->id }}-{{ $cost->container_no }}" class="rounded-lg shadow-xl w-full max-w-lg border border-gray-300">
                                    <form method="POST" action="{{ route('user.upload-file', $file->id) }}" enctype="multipart/form-data" class="bg-white rounded-md overflow-hidden">
                                        @csrf
                                        <!-- Header -->
                                        <div class="px-6 py-4 border-b">
                                            <h3 class="text-xl font-bold text-gray-800">Form Entry</h3>
                                        </div>
                                        <!-- Body -->
                                        <div class="px-6 py-6 space-y-5">
                                            <input type="hidden" name="jo_code" value="{{ $file->jo_code }}">
                                            <input type="hidden" name="container_no" value="{{ $cost->container_no }}">
                                            <div>
                                                <label class="block text-base font-medium text-gray-700 mb-1">Shipper Name</label>
                                                <input type="text" class="w-full border border-gray-300 rounded px-4 py-2 bg-gray-100 text-base" value="{{ $file->jobOrder->shipper_name ?? '-' }}" readonly>
                                            </div>
                                            <div>
                                                <label class="block text-base font-medium text-gray-700 mb-1">Container No</label>
                                                <input type="text" class="w-full border border-gray-300 rounded px-4 py-2 bg-gray-100 text-base" value="{{ $cost->container_no }}" readonly>
                                            </div>
                                            <div>
                                                <label class="block text-base font-medium text-gray-700 mb-1">Truck No</label>
                                                <input type="text" class="w-full border border-gray-300 rounded px-4 py-2 bg-gray-100 text-base" value="{{ $cost->truck_no }}" readonly>
                                            </div>
                                            <div>
                                                <label class="block text-base font-medium text-gray-700 mb-1">Surat Jalan (PDF / JPG)</label>
                                                <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" class="w-full border border-gray-300 rounded px-4 py-2 text-base bg-white" required>
                                            </div>
                                        </div>
                                        <!-- Footer -->
                                        <div class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-3">
                                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded text-base font-medium">Save changes</button>
                                            <button type="button" onclick="document.getElementById('uploadModal-{{ $file->id }}-{{ $cost->container_no }}').close()" class="bg-gray-500 text-white px-5 py-2.5 rounded text-base font-medium">Close</button>
                                        </div>
                                    </form>
                                </dialog>
                                @endif
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="13" class="text-center py-4 text-gray-500 border border-gray-300">No job orders available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
