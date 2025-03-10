<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    public function download($transaction)
    {
        $transaction = Transaction::with(['items.product', 'user'])
            ->where('id', $transaction)
            ->first();

        if (!$transaction) {
            return back()->with('error', 'Transaction not found');
        }

        $pdf = PDF::loadView('receipts.transaction', [
            'transaction' => $transaction
        ]);

        return $pdf->download('receipt-' . $transaction->code . '.pdf');
    }
}
