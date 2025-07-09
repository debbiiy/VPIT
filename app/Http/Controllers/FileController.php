<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VpitDoc;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Upload file dari form modal (berdasarkan jo_code & container_no)
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'jo_code' => 'required|string',
            'container_no' => 'required|string',
        ]);

        $doc = VpitDoc::where('jo_code', $request->jo_code)
                      ->where('container_no', $request->container_no)
                      ->firstOrFail();

        if ($request->hasFile('file')) {
            $filename = time() . '_' . $request->file('file')->getClientOriginalName();
            $request->file('file')->storeAs('files', $filename, 'public');

            $doc->file = $filename;
            $doc->is_status = 0;
            $doc->save();
        }

        return redirect()->back()->with('success', 'File uploaded successfully!');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'doc_id' => 'required|exists:tbl_vpit_doc,id',
            'file' => 'required|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $doc = VpitDoc::findOrFail($request->doc_id);

        $filename = time() . '_' . $request->file('file')->getClientOriginalName();
        $request->file('file')->storeAs('files', $filename, 'public');

        $doc->file = $filename;
        $doc->is_status = 0;
        $doc->save();

        return redirect()->back()->with('success', 'File uploaded successfully.');
    }
}
