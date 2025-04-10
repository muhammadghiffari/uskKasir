<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

    public function print($transaction)
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

        return $pdf->stream('receipt-' . $transaction->code . '.pdf');
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'period' => 'required|in:today,week,month,custom,all',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'cashier_id' => 'nullable|exists:users,id'
        ]);

        $query = Transaction::where('status', 'completed');

        // Apply period filter
        switch ($request->period) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                $periodText = 'Today (' . Carbon::today()->format('d M Y') . ')';
                break;
            case 'week':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                $periodText = 'This Week (' . Carbon::now()->startOfWeek()->format('d M Y') . ' - ' . Carbon::now()->endOfWeek()->format('d M Y') . ')';
                break;
            case 'month':
                $query->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                $periodText = 'This Month (' . Carbon::now()->startOfMonth()->format('d M Y') . ' - ' . Carbon::now()->endOfMonth()->format('d M Y') . ')';
                break;
            case 'custom':
                if ($request->date_from && $request->date_to) {
                    $query->whereBetween('created_at', [
                        Carbon::parse($request->date_from)->startOfDay(),
                        Carbon::parse($request->date_to)->endOfDay()
                    ]);
                    $periodText = 'Custom Period (' . Carbon::parse($request->date_from)->format('d M Y') . ' - ' . Carbon::parse($request->date_to)->format('d M Y') . ')';
                } else {
                    $periodText = 'All Time';
                }
                break;
            default:
                $periodText = 'All Time';
                break;
        }

        // Apply cashier filter
        if ($request->cashier_id) {
            $query->where('user_id', $request->cashier_id);
            $cashierName = User::find($request->cashier_id)->name;
        } else {
            $cashierName = 'All Cashiers';
        }

        $transactions = $query->with(['items.product', 'user'])->get();
        $totalSales = $transactions->sum('total_amount');
        $transactionCount = $transactions->count();

        $pdf = PDF::loadView('receipts.transactions-report', [
            'transactions' => $transactions,
            'period' => $periodText,
            'totalSales' => $totalSales,
            'transactionCount' => $transactionCount,
            'cashierName' => $cashierName
        ]);

        return $pdf->download('transactions-report-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }
}
