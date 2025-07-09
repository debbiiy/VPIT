<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VpitDoc;
use App\Models\VpitFin;
use App\Models\VpitFinDetail;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $docs = VpitDoc::with(['jobOrder', 'jobCost'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('container_no', 'like', '%' . $search . '%')
                      ->orWhereHas('jobOrder', function ($q2) use ($search) {
                          $q2->where('shipper_name', 'like', '%' . $search . '%');
                      })
                      ->orWhereHas('jobCost', function ($q3) use ($search) {
                          $q3->where('vendor', 'like', '%' . $search . '%')
                             ->orWhere('location', 'like', '%' . $search . '%');
                      });
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('dashboard-admin', compact('docs'));
    }

    public function approve($id)
    {
        $doc = VpitDoc::with('jobCost')->findOrFail($id);

        $doc->is_status = 1;
        $doc->approved = now();
        $doc->approved_by = Auth::user()->email;
        $doc->save();

        $nobkt = DB::table('tbl_vpit_fin_detail')
            ->where('jo_code', $doc->jo_code)
            ->where('container_no', $doc->container_no)
            ->value('nobkt');

        $alreadyExists = VpitFin::where(function ($query) use ($doc) {
            $query->where('vendor', $doc->vendor)
                  ->where('amount', $doc->jobCost->first()->amount ?? 0)
                  ->where('invoice', '-')
                  ->where('file', $doc->file);
        })->exists();

        if (!$alreadyExists) {
            $nobkt = $nobkt ?? 'VPFIN' . now()->format('YmdHis');

            VpitFin::create([
                'nobkt' => $nobkt,
                'vendor' => $doc->vendor,
                'amount' => $doc->jobCost->first()->amount ?? 0,
                'invoice' => '-',
                'file' => $doc->file,
                'received_date' => now()->format('Y-m-d'),
                'payment_date' => null,
                'payment_invoice' => null,
                'is_status' => 1,
                'created_by' => Auth::user()->email,
            ]);

            VpitFinDetail::updateOrCreate([
                'nobkt' => $nobkt,
                'jo_code' => $doc->jo_code,
                'container_no' => $doc->container_no,
            ]);
        }

        return redirect()->back()->with('success', 'Document approved & finance record created.');
    }
}