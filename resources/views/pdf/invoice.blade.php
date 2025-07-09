<!DOCTYPE html>
<html>
<head>
    <title>Approval Invoice</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        .center { text-align: center; }
        .qr { text-align: center; margin: 20px 0; }
    </style>
</head>
<body>
    <b>SAMUDERA INDONESIA</b><br>
    PT. PERUSAHAAN PELAYARAN NUSANTARA PANURJWAN

    <div class="qr">
        <img src="data:image/svg+xml;base64,{{ $qrSvg }}" width="100" alt="QR Code">
    </div>

    <h3 class="center">APPROVAL INVOICE VENDOR</h3>
    <p>
        <b>No</b> : {{ $fin->nobkt }}<br>
        <b>Vendor</b> : {{ $fin->vendorNameFromJobOrderCost->vendor ?? $fin->vendor ?? 'N/A' }}
    </p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Container No</th>
                <th>Shipper Name</th>
                <th>Vessel Voyage</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $i => $detail)
                @php
                    $doc = $docs->where('jo_code', $detail->jo_code)
                                ->where('container_no', $detail->container_no)
                                ->first();
                    $job = $doc?->jobOrder;
                    $jobCost = \App\Models\JobOrderCost::where('jo_code', $detail->jo_code)
                                ->where('container_no', $detail->container_no)
                                ->first();
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $detail->container_no }}</td>
                    <td>{{ $job->shipper_name ?? '-' }}</td>
                    <td>{{ $job->vessel ?? '-' }} {{ $job->voyage ?? '' }}</td>
                    <td>Rp {{ number_format($jobCost->amount ?? 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
