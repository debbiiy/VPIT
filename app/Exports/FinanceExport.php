<?php

namespace App\Exports;

use App\Models\VpitFin;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FinanceExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return VpitFin::select('nobkt', 'vendor', 'amount', 'invoice', 'received_date', 'payment_date')->get();
    }

    public function headings(): array
    {
        return ['Nobkt', 'Vendor', 'Amount', 'Invoice No', 'Received Date', 'Payment Date'];
    }
}

