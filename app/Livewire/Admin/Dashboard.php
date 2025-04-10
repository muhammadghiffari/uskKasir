<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;

class Dashboard extends Component
{
    use WithPagination;

    // Stats variables
    public $totalProducts;
    public $lowStockProducts;
    public $totalSales;
    public $todaySales;
    public $activeUsers;
    public $filteredTotalSales = 0;
    public $transactionCount = 0;
    public $totalItemsSold = 0;

    // Filters
    public $filterPeriod = 'today';
    public $filterCashier = '';
    public $dateFrom;
    public $dateTo;

    // Toggle transaction details
    public $expandedTransactions = [];

    // Listeners
    protected $listeners = ['refresh' => '$refresh'];

    public function mount()
    {
        $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');

        $this->loadStats();
    }

    private function loadStats()
    {
        $this->totalProducts = Product::count();
        $this->lowStockProducts = Product::where('stock', '<', 10)->count();
        $this->totalSales = Transaction::where('status', 'completed')->sum('total_amount');
        $this->todaySales = Transaction::where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount');
        $this->activeUsers = User::where('is_active', true)->count();
    }

    public function toggleTransactionDetails($transactionId)
    {
        if (in_array($transactionId, $this->expandedTransactions)) {
            $this->expandedTransactions = array_diff($this->expandedTransactions, [$transactionId]);
        } else {
            $this->expandedTransactions[] = $transactionId;
        }
    }

    public function printReceipt($transactionId)
    {
        return redirect()->route('receipt.download', $transactionId);
    }

    public function downloadTransactionsReport()
    {
        // Get the filtered transactions for the report
        $query = $this->getTransactionQuery();
        $transactions = $query->with(['items.product', 'user'])->get();

        // Generate the PDF
        $pdf = PDF::loadView('receipts.transactions-report', [
            'transactions'     => $transactions,
            'period'           => $this->getReportPeriodText(),
            'totalSales'       => $this->filteredTotalSales,
            'transactionCount' => $this->transactionCount,
            'dateFrom'         => $this->dateFrom,
            'dateTo'           => $this->dateTo,
            'cashierName'      => $this->filterCashier ? User::find($this->filterCashier)->name : 'All Cashiers'
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'transactions-report-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    public function downloadStockReport()
    {
        // Get all products with their current stock information
        $products = Product::orderBy('stock', 'asc')->get();

        // Generate the PDF
        $pdf = PDF::loadView('receipts.stock-report', [
            'products'         => $products,
            'lowStockProducts' => $this->lowStockProducts,
            'totalProducts'    => $this->totalProducts,
            'generatedDate'    => Carbon::now()->format('d M Y H:i'),
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'stock-report-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    private function getReportPeriodText()
    {
        switch ($this->filterPeriod) {
            case 'today':
                return 'Today (' . Carbon::today()->format('d M Y') . ')';
            case 'week':
                return 'This Week (' . Carbon::now()->startOfWeek()->format('d M Y') . ' - ' . Carbon::now()->endOfWeek()->format('d M Y') . ')';
            case 'month':
                return 'This Month (' . Carbon::now()->startOfMonth()->format('d M Y') . ' - ' . Carbon::now()->endOfMonth()->format('d M Y') . ')';
            case 'custom':
                return 'Custom Period (' . Carbon::parse($this->dateFrom)->format('d M Y') . ' - ' . Carbon::parse($this->dateTo)->format('d M Y') . ')';
            default:
                return 'All Time';
        }
    }

    private function getTransactionQuery()
    {
        $query = Transaction::where('status', 'completed');

        // Apply date filter
        switch ($this->filterPeriod) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'week':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                break;
            case 'custom':
                if ($this->dateFrom && $this->dateTo) {
                    $query->whereBetween('created_at', [
                        Carbon::parse($this->dateFrom)->startOfDay(),
                        Carbon::parse($this->dateTo)->endOfDay()
                    ]);
                }
                break;
        }

        // Apply cashier filter
        if ($this->filterCashier) {
            $query->where('user_id', $this->filterCashier);
        }

        return $query;
    }

    private function calculateItemsSold()
    {
        // Get transaction IDs from the current query
        $transactionIds = $this->getTransactionQuery()->pluck('id')->toArray();

        // Count total items sold for these transactions
        return TransactionItem::whereIn('transaction_id', $transactionIds)
            ->sum('quantity');
    }

    public function updatedFilterPeriod()
    {
        $this->resetPage();
    }

    public function updatedFilterCashier()
    {
        $this->resetPage();
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = $this->getTransactionQuery();

        // Calculate filtered totals
        $this->filteredTotalSales = $query->sum('total_amount');
        $this->transactionCount = $query->count();

        // Calculate total items sold based on current filters
        $this->totalItemsSold = $this->calculateItemsSold();

        // Get transactions with pagination
        $transactions = $query->with(['user', 'items.product'])
            ->latest()
            ->paginate(10);

        // Get all cashiers for the filter
        $cashiers = User::where('role', 'cashier')
            ->orWhere('role', 'admin')
            ->get();

        return view('livewire.admin.dashboard', [
            'transactions' => $transactions,
            'cashiers'     => $cashiers
        ]);
    }
}
