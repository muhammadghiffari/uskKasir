<?php

use App\Livewire\Admin\Dashboard;
use App\Livewire\CashierDashboard;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\ProductManager;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReceiptController;

// Redirect ke halaman login jika mengakses root
Route::get('/', function () {
    return redirect()->route('login');
});

// Middleware untuk autentikasi dan verifikasi
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Dashboard
    Route::get('/admin/dashboard', Dashboard::class)->name('admin.dashboard');
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Produk
    Route::get('admin/products', ProductManager::class)->name('admin.products');
    Route::resource('products', ProductController::class)->except(['show']);

    // Kasir
    Route::get('/kasir', CashierDashboard::class)->name('kasir');

    // Receipt and Reports
    Route::get('/print/receipt/{transaction}', [ReceiptController::class, 'print'])->name('print.receipt');
    Route::get('/download-receipt/{transaction}', [ReceiptController::class, 'download'])->name('receipt.download');
    Route::post('/generate-transactions-report', [ReceiptController::class, 'generateReport'])->name('transactions.report');
});
