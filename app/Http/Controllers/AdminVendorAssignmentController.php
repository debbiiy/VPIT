<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\VendorList;

class AdminVendorAssignmentController extends Controller
{
    public function index()
    {
        $users = User::where('role', '!=', 'admin')->whereNull('vendor_code')->get();
        $vendorList = VendorList::all();

        return view('vendor_assign', compact('users', 'vendorList'));
    }

    public function assign(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'vendor_code' => 'required|exists:vendor_list,vendor_code',
        ]);

        $user = User::find($request->user_id);
        $user->vendor_code = $request->vendor_code;
        $user->save();

        return redirect()->back()->with('success', 'Vendor code berhasil diberikan ke user.');
    }

    public function storeVendor(Request $request)
    {
        $request->validate([
            'vendor_code' => 'required|unique:vendor_list,vendor_code',
            'vendor_name' => 'required',
        ]);

        VendorList::create([
            'vendor_code' => $request->vendor_code,
            'vendor_name' => $request->vendor_name,
        ]);

        return redirect()->back()->with('success', 'Vendor baru berhasil ditambahkan.');
    }
}
