<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-2xl font-semibold mb-4 text-gray-800">Kasirin Dashboard</h2>

                    <!-- Transaction Completed Success Message -->
                    @if($transactionCompleted)
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm mb-4" role="alert">
                            <strong class="font-bold">Transaksi Berhasil!</strong>
                            <span class="block sm:inline">Pembayaran telah diproses.</span>
                            <div class="mt-2 flex space-x-2">
                                <button wire:click="printReceipt" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                                    <i class="fas fa-print mr-1"></i> Cetak Struk
                                </button>
                                <button wire:click="newTransaction" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                                    <i class="fas fa-plus mr-1"></i> Transaksi Baru
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Flash Messages -->
                    @if (session()->has('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-sm mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Product Selection Section -->
                        <div class="md:col-span-2">
                            <div class="bg-gray-50 p-4 rounded-lg shadow">
                                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 space-y-2 md:space-y-0">
                                    <h3 class="text-lg font-medium text-gray-800">Daftar Produk</h3>
                                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 w-full md:w-auto">
                                        <div class="relative">
                                            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari produk..."
                                                class="w-full md:w-64 pl-10 pr-4 py-2 border border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>

                                        <select wire:model.live="categoryFilter" class="w-full md:w-auto border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm py-2">
                                            <option value="">Semua Kategori</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                                    @foreach($products as $product)
                                        <div wire:click="$dispatch('product-selected', { productId: {{ $product->id }} })"
                                            class="bg-white p-4 rounded-lg shadow-sm cursor-pointer hover:bg-indigo-50 hover:shadow-md transition duration-200 border border-gray-100">
                                            <h4 class="font-medium text-gray-800 truncate">{{ $product->name }}</h4>
                                            <p class="text-gray-600 text-sm mt-1">Stok: {{ $product->stock }}</p>
                                            <p class="text-indigo-600 font-bold mt-2">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                        </div>
                                    @endforeach
                                </div>

                                @if($products->isEmpty())
                                    <div class="py-8 text-center">
                                        <p class="text-gray-500">Tidak ada produk yang ditemukan</p>
                                    </div>
                                @endif

                                <div class="mt-4">
                                    {{ $products->links() }}
                                </div>
                            </div>
                        </div>

                        <!-- Shopping Cart Section -->
                        <div class="md:col-span-1">
                            <div class="bg-gray-50 p-4 rounded-lg shadow">
                                <h3 class="text-lg font-medium mb-4 text-gray-800">Keranjang Belanja</h3>

                                @if(count($cart) > 0)
                                    <div class="space-y-3 mb-4 max-h-96 overflow-y-auto pr-1">
                                        @foreach($cart as $itemId => $item)
                                            <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-100">
                                                <div class="flex justify-between">
                                                    <h4 class="font-medium text-gray-800 truncate">{{ $item['name'] }}</h4>
                                                    <button wire:click="removeItem({{ $itemId }})" class="text-red-500 hover:text-red-700 transition-colors">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div class="flex justify-between items-center mt-2">
                                                    <div class="flex items-center space-x-2">
                                                        <button wire:click="decreaseQuantity({{ $itemId }})" class="bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded transition-colors">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                        <span class="font-medium text-gray-700">{{ $item['quantity'] }}</span>
                                                        <button wire:click="increaseQuantity({{ $itemId }})" class="bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded transition-colors">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="text-sm text-gray-600">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                                        <p class="font-bold text-indigo-600">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="border-t border-gray-200 pt-4 mt-4">
                                        <div class="flex justify-between text-lg font-bold text-gray-800">
                                            <span>Total:</span>
                                            <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                                        </div>

                                        <button wire:click="showPayment" class="w-full mt-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                            </svg>
                                            Proses Pembayaran
                                        </button>
                                    </div>
                                @else
                                    <div class="bg-white p-6 rounded-lg text-center border border-gray-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <p class="text-gray-500">Keranjang belanja kosong</p>
                                        <p class="text-gray-400 text-sm mt-1">Klik pada produk untuk menambahkannya</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    @if($showPaymentModal)
        <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Proses Pembayaran
                        </h3>

                        <div class="mt-4">
                            <div class="text-lg mb-4">
                                <div class="flex justify-between">
                                    <span>Total Belanja:</span>
                                    <span class="font-bold">Rp {{ number_format($total, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="cashAmount" class="block text-sm font-medium text-gray-700">Jumlah Uang</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">Rp</span>
                                    </div>
                                    <input type="number" id="cashAmount" wire:model.live="cashAmount" wire:input="calculateChange"
                                        class="pl-12 block w-full border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                </div>
                            </div>

                            <div class="text-lg">
                                <div class="flex justify-between">
                                    <span>Kembalian:</span>
                                    <span class="font-bold {{ $changeAmount < 0 ? 'text-red-600' : 'text-green-600' }}">
                                        Rp {{ number_format($changeAmount, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <!-- Tombol Bayar -->
                        <button wire:click="processPayment" type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200"
                            wire:loading.attr="disabled" @disabled($cashAmount < $total)>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor" wire:loading.remove wire:target="processPayment">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" wire:loading wire:target="processPayment">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="processPayment">Bayar</span>
                            <span wire:loading wire:target="processPayment">Memproses...</span>
                        </button>

                        <!-- Tombol Batal -->
                        <button wire:click="cancelPayment" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Alert for cart empty -->
    @if(session()->has('cart_empty'))
        <script>
            alert("Keranjang kosong! Tambahkan produk sebelum membayar.");
        </script>
    @endif

    <!-- Toast for product alerts -->
    <div id="alert-toast" class="fixed bottom-4 right-4 hidden transition-opacity duration-300 opacity-0">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-lg">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-red-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <span id="alert-message"></span>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('show-alert', (data) => {
                const toast = document.getElementById('alert-toast');
                const message = document.getElementById('alert-message');

                if (toast && message) {
                    message.textContent = data.message;
                    toast.classList.remove('hidden');
                    toast.classList.remove('opacity-0');
                    toast.classList.add('opacity-100');

                    setTimeout(() => {
                        toast.classList.remove('opacity-100');
                        toast.classList.add('opacity-0');
                        setTimeout(() => {
                            toast.classList.add('hidden');
                        }, 300);
                    }, 3000);
                }
            });
        });
    </script>
</div>
