<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public $totalProducts;
    public $lowStockProducts;
    public $recentTransactions;
    public $totalSales;
    public $todaySales;
    public $activeUsers;

    public function mount()
    {
        $this->totalProducts = Product::count();
        $this->lowStockProducts = Product::where('stock', '<', 10)->count();
        $this->recentTransactions = Transaction::with('user')
            ->where('status', 'completed')
            ->latest()
            ->take(5)
            ->get();
        $this->totalSales = Transaction::where('status', 'completed')->sum('total_amount');
        $this->todaySales = Transaction::where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount');
        $this->activeUsers = User::where('is_active', true)->count();
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
