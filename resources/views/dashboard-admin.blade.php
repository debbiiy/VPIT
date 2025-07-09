@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <div class="py-6 px-4">
        <div class="bg-white shadow-xl rounded-lg p-6 w-full border-l-4 border-blue-500">

            <!-- Title -->
            <h3 class="text-lg font-medium text-gray-700 mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-5 h-5 mr-2 text-gray-800" viewBox="0 0 24 24">
                    <path d="M16.862 3.487l3.651 3.651a1.25 1.25 0 010 1.768l-9.546 9.546a1.25 1.25 0 01-.547.317l-4.65 1.244a.5.5 0 01-.617-.617l1.244-4.65a1.25 1.25 0 01.317-.547l9.546-9.546a1.25 1.25 0 011.768 0zM5 20.75a.75.75 0 000 1.5h14a.75.75 0 000-1.5H5z"/>
                </svg>
                <b> Job Order </b>
            </h3>

            <!-- Filter -->
            <form method="GET" action="{{ route('dashboard-admin') }}" class="mb-4 flex items-center gap-2">
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
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 border border-gray-300">
                            <th class="p-3 border border-gray-300">No</th>
                            <th class="p-3 border border-gray-300">Jo No</th>
                            <th class="p-3 border border-gray-300">Shipper</th>
                            <th class="p-3 border border-gray-300">Vendor</th>
                            <th class="p-3 border border-gray-300">Location</th>
                            <th class="p-3 border border-gray-300">Price</th>
                            <th class="p-3 border border-gray-300">Container</th>
                            <th class="p-3 border border-gray-300">Ctr Code</th>
                            <th class="p-3 border border-gray-300">Truck</th>
                            <th class="p-3 border border-gray-300">Stuffing</th>
                            <th class="p-3 border border-gray-300">File</th>
                            <th class="p-3 border border-gray-300">Status</th>
                            <th class="p-3 border border-gray-300">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                       @forelse($docs as $index => $file)
                            @php
                                $cost = $file->jobCost->where('container_no', $file->container_no)->first();
                            @endphp
                            <tr class="hover:bg-gray-50 border border-gray-300">
                                <td class="p-3 border border-gray-300">{{ $index + $docs->firstItem() }}</td>
                                <td class="p-3 border border-gray-300">{{ $file->jobOrder->code_manual ?? '-' }}</td>
                                <td class="p-3 border border-gray-300">{{ $file->jobOrder->shipper_name ?? '-' }}</td>
                                <td class="p-3 border border-gray-300">{{ $cost->vendor ?? '-' }}</td>
                                <td class="p-3 border border-gray-300">{{ $cost->location ?? '-' }}</td>
                                <td class="p-3 border border-gray-300">Rp {{ number_format($cost->amount ?? 0) }}</td>
                                <td class="p-3 border border-gray-300">{{ $file->container_no ?? '-' }}</td>
                                <td class="p-3 border border-gray-300">{{ $cost->container_size ?? '-' }}</td>
                                <td class="p-3 border border-gray-300">{{ $cost->truck_no ?? '-' }}</td>
                                <td class="p-3 border border-gray-300">{{ $cost->stuffing_place ?? '-' }}</td>
                                <td class="p-3 border border-gray-300">
                                    @if($file->file)
                                        <a href="{{ asset('storage/files/' . $file->file) }}" target="_blank">
                                            <i class="fa-regular fa-eye text-black-500 hover:text-gray-800 text-base"></i>
                                        </a>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Empty</span>
                                    @endif
                                </td>
                                <td class="p-2 border border-gray-300">
                                    <span class="px-2 py-1 text-xs rounded {{ $file->is_status == 1 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $file->is_status == 1 ? 'Approved' : 'Waiting' }}
                                    </span>
                                </td>
                                <td class="p-2 border border-gray-300 text-center">
                                    @if($file->is_status == 0)
                                        <form method="POST" action="{{ route('admin.approve', $file->id) }}">
                                            @csrf
                                            <input type="checkbox" class="approve-checkbox" onchange="this.form.submit()">
                                        </form>
                                    @else
                                        <span class="text-gray-400">✔️</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="text-center py-4 text-gray-500 border border-gray-300">No job orders available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $docs->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    input[type="checkbox"].approve-checkbox:checked {
        accent-color: #16a34a;
    }
</style>
@endsection
