<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VpitDocController;
use App\Http\Controllers\VpitFinController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\AdminDashboardController;
use Illuminate\Http\Request;

// Default redirect
Route::get('/', function () {
    return redirect('/login');
});


// ==========================
// USER ROUTES
// ==========================
Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    // Document
    Route::get('/documents', [VpitDocController::class, 'index'])->name('doc.index');
    Route::post('/documents/upload', [VpitDocController::class, 'store'])->name('doc.store');

    // Upload file
    Route::post('/upload-file/{id}', [FileController::class, 'store'])->name('upload-file');
    Route::post('/upload', [FileController::class, 'upload'])->name('upload.submit');

    // Finance
    Route::get('/finance', [VpitFinController::class, 'userIndex'])->name('fin.index');
    Route::get('/finance/{nobkt}', [VpitFinController::class, 'userShow'])->name('fin.show');
    Route::post('/finance', [VpitFinController::class, 'store'])->name('fin.store'); // Create finance from user
    Route::get('/finance/pdf/{nobkt}', [VpitFinController::class, 'generatePdf'])->name('finance.pdf');


    // Dashboard
    Route::get('/dashboard', function (Request $request) {
    $query = \App\Models\VpitDoc::with(['jobOrder', 'jobCost']);

    if (auth()->user()->role !== 'admin') {
        $vendorCode = auth()->user()->vendor_code;

        if (!$vendorCode) {
            return view('user.vendor_pending');
        }
        $query->where('vendor', $vendorCode);
    }
    
    if ($request->has('search') && $request->search !== '') {
        $keyword = $request->search;
        $query->where(function ($q) use ($keyword) {
            $q->where('jo_code', 'like', "%$keyword%")
                ->orWhere('container_no', 'like', "%$keyword%")
                ->orWhereHas('jobOrder', function ($q2) use ($keyword) {
                    $q2->where('shipper_name', 'like', "%$keyword%");
                })
                ->orWhereHas('jobCost', function ($q3) use ($keyword) {
                    $q3->where('vendor', 'like', "%$keyword%")
                        ->orWhere('location', 'like', "%$keyword%")
                        ->orWhere('truck_no', 'like', "%$keyword%")
                        ->orWhere('stuffing_place', 'like', "%$keyword%");
                });
        });
    }

    $files = $query->get();

    return view('dashboard-user', compact('files'));
    })->name('dashboard');
});

// ==========================
// ADMIN ROUTES
// ==========================
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard-admin');
    Route::post('/dashboard/approve/{id}', [AdminDashboardController::class, 'approve'])->name('admin.approve');

    // Document management
    Route::get('/documents', [VpitDocController::class, 'adminIndex'])->name('admin.doc.index');
    Route::patch('/documents/{id}/status', [VpitDocController::class, 'updateStatus'])->name('admin.doc.status');
    Route::post('/document/approve/{id}', [AdminDashboardController::class, 'approve'])->name('admin.document.approve');

    // Finance management
    Route::prefix('finance')->name('admin.fin.')->group(function () {
        Route::get('/', [VpitFinController::class, 'adminIndex'])->name('index');
        Route::get('/export', [VpitFinController::class, 'exportExcel'])->name('export');
        Route::get('/{nobkt}', [VpitFinController::class, 'show'])->name('show');
        Route::post('/upload', [VpitFinController::class, 'store'])->name('store');
        Route::post('/upload-invoice/{nobkt}', [VpitFinController::class, 'uploadInvoice'])->name('uploadInvoice');
    });
});


// ==========================
// USER PROFILE
// ==========================
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/home', function () {
    return view('home');
})  ->middleware(['auth'])->name('home');

require __DIR__.'/auth.php';
