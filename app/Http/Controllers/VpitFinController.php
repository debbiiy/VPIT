<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\VpitDoc;
use App\Models\VpitFin;
use App\Models\VpitFinDetail;
use App\Models\JobOrderCost;
use App\Exports\FinanceExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class VpitFinController extends Controller
{
    public function adminIndex(Request $request)
    {
        $query = VpitFin::with(['details'])->orderByDesc('nobkt');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nobkt', 'like', '%' . $request->search . '%')
                  ->orWhere('vendor', 'like', '%' . $request->search . '%');
            });
        }
        $finance = $query->paginate(10);
        return view('admin.finance.index', compact('finance'));
    }

    public function exportExcel()
    {
        return Excel::download(new FinanceExport, 'finance_data.xlsx');
    }

    public function show($nobkt)
    {
        $fin = VpitFin::where('nobkt', $nobkt)->firstOrFail();
        $details = VpitFinDetail::where('nobkt', $nobkt)->get();
        $docs = VpitDoc::whereIn('jo_code', $details->pluck('jo_code'))
                       ->whereIn('container_no', $details->pluck('container_no'))
                       ->get();

        return view('admin.finance.detail', compact('fin', 'details', 'docs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_no' => 'required|string',
            'invoice_file' => 'required|file|mimes:pdf,jpg,jpeg,png',
            'jo_codes' => 'required|array',
            'container_nos' => 'required|array',
            'shippers' => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            $now = now();
            $prefix = 'VPFIN' . $now->format('ym');

            $lastCounter = VpitFin::where('nobkt', 'like', $prefix . '%')
                ->lockForUpdate()
                ->selectRaw('MAX(CAST(SUBSTRING(nobkt, -5) AS UNSIGNED)) as max_counter')
                ->value('max_counter');

            $counter = $lastCounter ? $lastCounter + 1 : 1;
            $nobkt = $prefix . str_pad($counter, 5, '0', STR_PAD_LEFT);

            $file = $request->file('invoice_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('file_invoice', $filename, 'public');

            $firstDoc = VpitDoc::where('jo_code', $request->jo_codes[0])
                ->where('container_no', $request->container_nos[0])
                ->first();
            $vendor = $firstDoc ? $firstDoc->vendor : 'Unknown';

            $amount = 0;
            foreach ($request->jo_codes as $index => $jo) {
                $container = $request->container_nos[$index];
                $jobCost = JobOrderCost::where('jo_code', $jo)->where('container_no', $container)->first();
                $amount += $jobCost ? $jobCost->amount : 0;
            }

            $fin = new VpitFin();
            $fin->nobkt = $nobkt;
            $fin->vendor = $vendor;
            $fin->amount = $amount;
            $fin->invoice = $request->invoice_no;
            $fin->file = $filename;
            $fin->received_date = now();
            $fin->is_status = 1;
            $fin->created_by = auth()->user()->email;
            $fin->save();

            foreach ($request->jo_codes as $index => $jo) {
                VpitFinDetail::create([
                    'nobkt' => $nobkt,
                    'jo_code' => $jo,
                    'container_no' => $request->container_nos[$index],
                ]);
            }
            DB::commit();
            return redirect()->route('user.fin.index')->with('success', 'Finance data saved successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function uploadInvoice(Request $request, $nobkt)
    {
        $request->validate([
            'payment_invoice' => 'required|file|mimes:pdf,jpg,png,jpeg',
        ]);

        $fin = VpitFin::where('nobkt', $nobkt)->firstOrFail();

        $filename = time() . '_' . $request->file('payment_invoice')->getClientOriginalName();
        $request->file('payment_invoice')->storeAs('payment_invoice', $filename, 'public');

        $fin->payment_invoice = $filename;
        $fin->payment_date = now();
        $fin->save();

        return redirect()->back()->with('success', 'Invoice pembayaran berhasil diunggah.');
    }

    public function userIndex()
    {
        $user = auth()->user();

        if (empty($user->vendor_code)) {
            return view('user.vendor_pending');
        }

        $finance = VpitFin::where('created_by', $user->email)->orderByDesc('nobkt')->paginate(10);

        $availableDocs = VpitDoc::with('jobOrder')
            ->where('is_status', 1)
            ->where('created_by', $user->email)
            ->get();

        return view('user.finance.index', compact('finance', 'availableDocs'));
    }


    public function generatePdf($nobkt)
    {
        $fin = VpitFin::where('nobkt', $nobkt)->firstOrFail();
        $details = VpitFinDetail::where('nobkt', $nobkt)->get();
        $docs = VpitDoc::whereIn('jo_code', $details->pluck('jo_code'))
                    ->whereIn('container_no', $details->pluck('container_no'))
                    ->get();

        $qrSvg = base64_encode(
            QrCode::format('svg')->size(150)->generate($fin->nobkt)
        );

        return Pdf::loadView('pdf.invoice', compact('fin', 'details', 'docs', 'qrSvg'))
                ->stream("Invoice-{$fin->nobkt}.pdf");
    }
}
