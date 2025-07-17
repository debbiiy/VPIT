@extends('layouts.app')

@section('title', 'Finance Page')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/alpinejs" defer></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

<div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <div class="py-6 px-4">
        <div class="bg-white shadow-xl rounded-lg p-6 w-full border-l-4 border-blue-500">
            <h3 class="text-lg font-medium text-gray-700 mb-4 flex items-center space-x-2">
                <i class="fas fa-truck text-grey-600"></i>
                <span><b>Trucking Management (Finance)</b></span>
            </h3>

            <div class="flex justify-between items-center mb-4">
                <form method="GET" action="{{ route('admin.fin.index') }}" class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Nobkt..." class="px-3 py-2 border-gray-300 rounded-md focus:ring focus:ring-blue-200">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Search</button>
                </form>

                <a href="{{ route('admin.fin.export') }}" class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 flex items-center gap-2">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full table-fixed text-sm text-left border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700">
                            <th class="p-3 border">No</th>
                            <th class="p-3 border">Nobkt</th>
                            <th class="p-3 border">Vendor</th>
                            <th class="p-3 border">Amount</th>
                            <th class="p-3 border">Invoice No</th>
                            <th class="p-3 border">Received Inv.</th>
                            <th class="p-3 border">Payment Date</th>
                            <th class="p-3 border">Payment Inv.</th>
                            <th class="p-3 border">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($finance as $index => $fin)
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 border text-center">{{ $loop->iteration + ($finance->currentPage() - 1) * $finance->perPage() }}</td>
                            <td class="p-3 border">{{ $fin->nobkt }}</td>
                            <td class="p-3 border">
                                {{ $fin->vendorNameFromJobOrderCost->vendor ?? $fin->vendor ?? 'N/A' }}
                            </td>
                            <td class="p-3 border">Rp {{ number_format($fin->amount, 0, ',', '.') }}</td>
                            <td class="p-3 border">{{ $fin->invoice }}</td>
                            <td class="p-3 border">{{ \Carbon\Carbon::parse($fin->received_date)->format('d-m-Y') }}</td>
                            <td class="p-3 border">
                                @if($fin->payment_date && $fin->payment_date != '0000-00-00')
                                    {{ \Carbon\Carbon::parse($fin->payment_date)->format('d-m-Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="p-3 border text-center">
                                @if ($fin->payment_invoice)
                                    <a href="{{ asset('storage/payment_invoice/' . $fin->payment_invoice) }}" target="_blank">
                                        <i class="fa fa-eye text-blue-700 text-lg" title="Lihat Payment Invoice"></i>
                                    </a>
                                @else
                                    <form action="{{ route('admin.fin.uploadInvoice', $fin->nobkt) }}" method="POST" enctype="multipart/form-data" id="form-{{ $fin->id }}">
                                        @csrf
                                        <input type="file" name="payment_invoice" class="hidden" id="input-{{ $fin->id }}" onchange="document.getElementById('form-{{ $fin->id }}').submit();" required>
                                        <label for="input-{{ $fin->id }}" class="cursor-pointer text-blue-600">
                                            <i class="fa fa-upload text-lg" title="Upload Payment Invoice"></i>
                                        </label>
                                    </form>
                                @endif
                            </td>
                            <td class="p-3 border">
                                <div x-data="{ showModal: false }">
                                    <button @click="showModal = true" class="bg-blue-500 text-white px-2 py-1 rounded">Detail</button>

                                    <!-- Modal -->
                                    <div x-show="showModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                                        <div class="bg-white rounded-lg shadow-lg w-full max-w-5xl p-6" @click.away="showModal = false">
                                            <h2 class="text-lg font-bold mb-4">Data Detail</h2>

                                            <div class="grid grid-cols-2 gap-4 mb-4">
                                                <div><strong>Document No:</strong> {{ $fin->nobkt }}</div>
                                                <div><strong>Invoice No:</strong> {{ $fin->invoice }}</div>
                                                <div>
                                                    <strong>Invoice File:</strong>
                                                    @if ($fin->file)    
                                                        <a href="{{ asset('storage/file_invoice/' . $fin->file) }}" target="_blank" class="text-green-700">📄</a>
                                                    @else
                                                        <span class="text-gray-400">N/A</span>
                                                    @endif
                                                </div>
                                                <div><strong>Amount:</strong> Rp {{ number_format($fin->amount, 0, ',', '.') }}</div>
                                            </div>

                                            <h3 class="font-semibold mb-2">Job Order Details</h3>
                                            <table class="w-full table-fixed border border-gray-300 text-sm mb-4">
                                                <thead class="bg-gray-200">
                                                    <tr>
                                                        <th class="p-3 border">No</th>
                                                        <th class="p-3 border">Jo No</th>
                                                        <th class="p-3 border">Jo Manual</th>
                                                        <th class="p-3 border">Container No</th>
                                                        <th class="p-3 border">Shipper</th>
                                                        <th class="p-3 border">S. Jalan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($fin->details as $i => $detail)
                                                    @php
                                                        $jobOrder = \App\Models\JobOrder::where('code', $detail->jo_code)->first();
                                                        $doc = \App\Models\VpitDoc::where('jo_code', $detail->jo_code)
                                                            ->where('container_no', $detail->container_no)
                                                            ->where('is_status', 1)
                                                            ->first();
                                                    @endphp
                                                    @if ($doc)
                                                    <tr>
                                                        <td class="p-3 border">{{ $i + 1 }}</td>
                                                        <td class="p-3 border">{{ $jobOrder->code ?? '-' }}</td> 
                                                        <td class="p-3 border">{{ $jobOrder->code_manual ?? '-' }}</td> 
                                                        <td class="p-3 border">{{ $doc->container_no ?? '-' }}</td> 
                                                        <td class="p-3 border">{{ $jobOrder->shipper_name ?? '-' }}</td>
                                                        <td class="p-3 border text-center">
                                                            @if ($doc->file)
                                                                <a href="{{ asset('storage/files/' . $doc->file) }}" target="_blank" class="text-green-700">📄</a>
                                                            @else
                                                                <span class="text-gray-400">N/A</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endif
                                                @endforeach
                                                </tbody>
                                            </table>
                                            <div class="text-right">
                                                <button @click="showModal = false" class="bg-gray-500 text-white px-4 py-2 rounded">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-gray-500">No finance records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $finance->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
