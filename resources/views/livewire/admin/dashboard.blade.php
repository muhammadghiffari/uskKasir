<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if (session()->has('message'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('message') }}</span>
                </div>
            @endif

            <!-- Dashboard Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-indigo-500 text-white">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-600 text-sm font-medium">Total Products</p>
                            <p class="text-2xl font-bold">{{ $totalProducts }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-500 text-white">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-600 text-sm font-medium">Low Stock Items</p>
                            <p class="text-2xl font-bold">{{ $lowStockProducts }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-500 text-white">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-600 text-sm font-medium">Today's Sales</p>
                            <p class="text-2xl font-bold">Rp {{ number_format($todaySales, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-500 text-white">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-600 text-sm font-medium">Active Users</p>
                            <p class="text-2xl font-bold">{{ $activeUsers }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions with Filtering -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                    <h3 class="text-lg font-semibold mb-2 md:mb-0">Recent Transactions</h3>

                    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-2">
                        <!-- Transaction Filters -->
                        <div class="flex flex-wrap gap-2">
                            <select wire:model.live="filterPeriod" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                <option value="all">All Time</option>
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                                <option value="custom">Custom Date</option>
                            </select>

                            @if($filterPeriod === 'custom')
                            <div class="flex gap-2">
                                <input type="date" wire:model.live="dateFrom" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                <input type="date" wire:model.live="dateTo" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            </div>
                            @endif

                            <select wire:model.live="filterCashier" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                <option value="">All Cashiers</option>
                                @foreach($cashiers as $cashier)
                                    <option value="{{ $cashier->id }}">{{ $cashier->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Print All Button -->
                        <button
                            wire:click="downloadTransactionsReport"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                        >
                            <i class="fas fa-file-pdf mr-1"></i> Cetak Rekap
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Code</th>
                                <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Cashier</th>
                                <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                                <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Total</th>
                                <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                                <tr>
                                    <td class="py-2 px-4 border-b border-gray-200">{{ $transaction->code }}</td>
                                    <td class="py-2 px-4 border-b border-gray-200">{{ $transaction->user->name }}</td>
                                    <td class="py-2 px-4 border-b border-gray-200">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                    <td class="py-2 px-4 border-b border-gray-200">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                                    <td class="py-2 px-4 border-b border-gray-200">
                                        <button
                                            wire:click="toggleTransactionDetails({{ $transaction->id }})"
                                            class="text-blue-500 hover:text-blue-700 mr-2"
                                        >
                                            {{ in_array($transaction->id, $expandedTransactions) ? 'Sembunyikan' : 'Lihat Detail' }}
                                        </button>
                                    </td>
                                </tr>

                                @if(in_array($transaction->id, $expandedTransactions))
                                <tr>
                                    <td colspan="5" class="py-4 px-4 border-b border-gray-200 bg-gray-50">
                                        <div class="mb-4">
                                            <div class="flex justify-between mb-2">
                                                <h4 class="font-semibold">Transaction Details</h4>
                                                <button
                                                    wire:click="printReceipt({{ $transaction->id }})"
                                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded"
                                                >
                                                    <i class="fas fa-print mr-1"></i> Cetak Struk
                                                </button>
                                            </div>

                                            <div class="bg-white p-3 rounded shadow-sm">
                                                <div class="grid grid-cols-2 gap-4 mb-3">
                                                    <div>
                                                        <p><span class="font-semibold">Transaction ID:</span> {{ $transaction->code }}</p>
                                                        <p><span class="font-semibold">Date:</span> {{ $transaction->created_at->format('d M Y H:i') }}</p>
                                                        <p><span class="font-semibold">Cashier:</span> {{ $transaction->user->name }}</p>
                                                    </div>
                                                    <div>
                                                        <p><span class="font-semibold">Total Amount:</span> Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                                                        <p><span class="font-semibold">Payment Amount:</span> Rp {{ number_format($transaction->payment_amount, 0, ',', '.') }}</p>
                                                        <p><span class="font-semibold">Change Amount:</span> Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</p>
                                                    </div>
                                                </div>

                                                <h5 class="font-semibold mb-2">Items</h5>
                                                <table class="min-w-full bg-white border">
                                                    <thead>
                                                        <tr>
                                                            <th class="py-1 px-2 border-b text-left text-xs font-semibold text-gray-600">Product</th>
                                                            <th class="py-1 px-2 border-b text-right text-xs font-semibold text-gray-600">Price</th>
                                                            <th class="py-1 px-2 border-b text-right text-xs font-semibold text-gray-600">Qty</th>
                                                            <th class="py-1 px-2 border-b text-right text-xs font-semibold text-gray-600">Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($transaction->items as $item)
                                                            <tr>
                                                                <td class="py-1 px-2 border-b">{{ $item->product->name }}</td>
                                                                <td class="py-1 px-2 border-b text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                                                <td class="py-1 px-2 border-b text-right">{{ $item->quantity }}</td>
                                                                <td class="py-1 px-2 border-b text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5" class="py-2 px-4 border-b border-gray-200 text-center">No transactions found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $transactions->links() }}
                </div>
            </div>

            <!-- Quick Links -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="{{ route('admin.products') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                            Manage Products
                        </a>
                                    <button wire:click="downloadStockReport"
                                        class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded text-center">
                                        Generate Stock Report
                                    </button>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Stats Summary</h3>
                    <p class="mb-2"><span class="font-medium">Total Sales:</span> Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
                    <p class="mb-2"><span class="font-medium">{{ $filterPeriod === 'today' ? "Today's" : ($filterPeriod === 'week' ? 'This Week\'s' : ($filterPeriod === 'month' ? 'This Month\'s' : 'Filtered')) }} Sales:</span> Rp {{ number_format($filteredTotalSales, 0, ',', '.') }}</p>
                    <p><span class="font-medium">Total Items Sold:</span> {{ number_format($totalItemsSold, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
