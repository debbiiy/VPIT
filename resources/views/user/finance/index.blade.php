@extends('layouts.app')
@php
    use Carbon\Carbon;
@endphp

@section('title', 'User Finance')

@section('content')
<div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <div class="py-6 px-4">
        <div class="bg-white shadow-lg rounded-lg p-6 border-l-4 border-blue-500">
            <h3 class="text-lg font-medium text-gray-700 mb-4 flex items-center space-x-2">
                <i class="fas fa-truck text-grey-600"></i>
                <span><b>Trucking Management (Finance)</b></span>
            </h3>

            <div class="mb-4 flex justify-end">
                <button onclick="document.getElementById('createModal').showModal()" class="bg-blue-600 text-white px-4 py-2 rounded shadow">Create New</button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full table-auto text-sm border border-gray-300">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="p-2 border">No</th>
                            <th class="p-2 border">Nobkt</th>
                            <th class="p-2 border">Amount</th>
                            <th class="p-2 border">Sum of Container</th>
                            <th class="p-2 border">Status</th>
                            <th class="p-2 border">Received Inv.</th>
                            <th class="p-2 border">Payment Date</th>
                            <th class="p-2 border">Payment Inv.</th>
                            <th class="p-2 border">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($finance as $fin)
                            <tr class="border hover:bg-gray-50">
                                <td class="p-2 border text-center">{{ $loop->iteration }}</td>
                                <td class="p-2 border">{{ $fin->nobkt }}</td>
                                <td class="p-2 border">Rp {{ number_format($fin->amount) }}</td>
                                <td class="p-2 border text-center">{{ $fin->details->count() }}</td>
                                <td class="p-2 border text-center">
                                    <span class="px-2 py-1 text-xs rounded {{ $fin->is_status ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $fin->is_status ? 'Approved' : 'Waiting' }}
                                    </span>
                                </td>
                                <td class="p-2 border">{{ $fin->received_date ? Carbon::parse($fin->received_date)->format('d-m-Y') : '-' }}</td>
                                <td class="p-2 border">{{ $fin->payment_date ? Carbon::parse($fin->payment_date)->format('d-m-Y') : '-' }}</td>
                                <td class="p-2 border text-center">
                                    @if($fin->payment_invoice)
                                        <a href="{{ asset('storage/payment_invoice/' . $fin->payment_invoice) }}" target="_blank">
                                            <i class="fa fa-file text-green-700"></i>
                                        </a>
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </td>
                               <td class="text-center">
                                    @if($fin->payment_invoice)
                                       <a href="{{ route('user.finance.pdf', $fin->nobkt) }}" target="_blank"
                                        class="bg-green-600 text-white text-sm px-3 py-1 rounded hover:bg-green-700"
                                        title="Download Approval Invoice">
                                            <i class="fa fa-download"></i>
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-sm italic">Waiting</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-gray-500 py-4">No finance data</td></tr>
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

<dialog id="createModal" class="rounded-lg shadow-xl w-full max-w-5xl">
    <form id="financeForm" method="POST" action="{{ route('user.fin.store') }}" enctype="multipart/form-data" class="p-6">
        @csrf
        <h2 class="text-xl font-bold mb-4">Form Entry</h2>
        <div class="mb-4">
            <label class="block font-medium mb-1">Invoice No</label>
            <input type="text" name="invoice_no" class="w-full border px-3 py-2 rounded" required>
        </div>
        <div class="mb-4">
            <label class="block font-medium mb-1">Invoice File</label>
            <input type="file" name="invoice_file" class="w-full border px-3 py-2 rounded" required>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="font-semibold block mb-1">Available Containers (Approved only)</label>
                <table class="min-w-full border text-sm">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="border p-2">No</th>
                            <th class="border p-2">Container No</th>
                            <th class="border p-2">Shipper</th>
                            <th class="border p-2 text-center">
                                Select <input type="checkbox" id="selectAllAvailable" onclick="toggleAllAvailable(this)">
                            </th>
                        </tr>
                    </thead>
                    <tbody id="availableTable">
                        @foreach($availableDocs as $i => $doc)
                            @php $shipper = $doc->jobOrder->shipper_name ?? '-'; @endphp
                            <tr>
                                <td class="border p-2">{{ $i + 1 }}</td>
                                <td class="border p-2">{{ $doc->container_no }}</td>
                                <td class="border p-2">{{ $shipper }}</td>
                                <td class="border p-2 text-center">
                                    <input type="checkbox" value="{{ $doc->jo_code }}||{{ $doc->container_no }}||{{ $shipper }}||{{ $i + 1 }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <button type="button" onclick="attachSelected()" class="mt-2 bg-blue-600 text-white px-4 py-1 rounded">Attach</button>
            </div>
            <div>
                <label class="font-semibold block mb-1">Attached (Preview)</label>
                <table class="min-w-full border text-sm">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="border p-2">No</th>
                            <th class="border p-2">Container No</th>
                            <th class="border p-2">Shipper</th>
                            <th class="border p-2 text-center">
                                Action <input type="checkbox" id="selectAllAttached" onclick="toggleAllAttached(this)">
                            </th>
                        </tr>
                    </thead>
                    <tbody id="attachedTable"></tbody>
                </table>
                <button type="button" onclick="detachSelected()" class="mt-2 bg-red-600 text-white px-4 py-1 rounded">Detach Selected</button>
            </div>
        </div>
        <div class="mt-4 flex justify-end space-x-2">
            <button type="button" onclick="submitWithHiddenInputs()" class="bg-green-600 text-white px-4 py-2 rounded">Save</button>
            <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded" onclick="document.getElementById('createModal').close()">Close</button>
        </div>
    </form>
</dialog>

<script>
    let attachedData = [];

    function toggleAllAvailable(source) {
        document.querySelectorAll('#availableTable input[type="checkbox"]').forEach(cb => cb.checked = source.checked);
    }

    function toggleAllAttached(source) {
        document.querySelectorAll('#attachedTable input[type="checkbox"]').forEach(cb => cb.checked = source.checked);
    }

    function attachSelected() {
        const checkboxes = document.querySelectorAll('#availableTable input[type="checkbox"]:checked');
        const attachedTable = document.getElementById('attachedTable');

        checkboxes.forEach((checkbox) => {
            const [jo, cont, shipper, num] = checkbox.value.split('||');
            const row = checkbox.closest('tr');
            attachedData.push({ jo_code: jo, container_no: cont, shipper: shipper });

            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td class="border p-2">${attachedTable.children.length + 1}</td>
                <td class="border p-2">${cont}</td>
                <td class="border p-2">${shipper}</td>
                <td class="border p-2 text-center">
                    <input type="checkbox" value="${jo}||${cont}||${shipper}||${num}">
                </td>
            `;
            attachedTable.appendChild(newRow);
            row.remove();
        });

        document.getElementById('selectAllAvailable').checked = false; // Reset checkbox
        updateHiddenInputs();
    }

    function detachSelected() {
        const checkboxes = document.querySelectorAll('#attachedTable input[type="checkbox"]:checked');
        const availableTable = document.getElementById('availableTable');

        checkboxes.forEach((checkbox) => {
            const [jo, cont, shipper, no] = checkbox.value.split('||');
            const row = checkbox.closest('tr');
            row.remove();

            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td class="border p-2">${no}</td>
                <td class="border p-2">${cont}</td>
                <td class="border p-2">${shipper}</td>
                <td class="border p-2 text-center">
                    <input type="checkbox" value="${jo}||${cont}||${shipper}||${no}">
                </td>
            `;
            availableTable.appendChild(newRow);

            attachedData = attachedData.filter(item => !(item.jo_code === jo && item.container_no === cont));
        });

        document.getElementById('selectAllAttached').checked = false; // Reset checkbox
        updateHiddenInputs();
    }

    function updateHiddenInputs() {
        const form = document.getElementById('financeForm');
        form.querySelectorAll('input[name="jo_codes[]"], input[name="container_nos[]"], input[name="shippers[]"]').forEach(i => i.remove());

        attachedData.forEach(data => {
            const fields = ['jo_codes', 'container_nos', 'shippers'];
            [data.jo_code, data.container_no, data.shipper].forEach((val, idx) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = fields[idx] + '[]';
                input.value = val;
                form.appendChild(input);
            });
        });
    }

    function submitWithHiddenInputs() {
        updateHiddenInputs();
        const btn = document.querySelector('#financeForm button[onclick="submitWithHiddenInputs()"]');
        btn.disabled = true;
        btn.innerText = 'Processing...';
        setTimeout(() => document.getElementById('financeForm').submit(), 100);
    }
</script>
@endsection
