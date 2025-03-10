<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReceiptController;
use App\Livewire\CashierDashboard;
use Illuminate\Support\Facades\Route;

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
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Produk
    Route::resource('products', ProductController::class)->except(['show']);

    // Kasir
    Route::get('/kasir', CashierDashboard::class)->name('kasir');

    // Struk transaksi
    Route::get('/print/receipt/{transaction}', [ReceiptController::class, 'print'])->name('print.receipt');
    Route::get('/download-receipt/{transaction}', [ReceiptController::class, 'download'])->name('receipt.download');
});
