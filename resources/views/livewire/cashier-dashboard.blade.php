<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-2xl font-semibold mb-4">Kasirin Dashboard</h2>

                    <!-- Transaction Completed Success Message -->
                    @if($transactionCompleted)
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Transaksi Berhasil!</strong>
                            <span class="block sm:inline">Pembayaran telah diproses.</span>
                            <div class="mt-2 flex space-x-2">
                                <button wire:click="printReceipt" class="bg-blue-500 hover:bg-blue-700 text-black font-bold py-2 px-4 rounded">
                                    <i class="fas fa-print mr-1"></i> Cetak Struk
                                </button>
                                <button wire:click="newTransaction" class="bg-green-500 hover:bg-green-700 text-black font-bold py-2 px-4 rounded">
                                    <i class="fas fa-plus mr-1"></i> Transaksi Baru
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Flash Messages -->
                    @if (session()->has('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Product Selection Section -->
                        <div class="md:col-span-2">
                            <div class="bg-gray-50 p-4 rounded-lg shadow">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium">Daftar Produk</h3>
                                    <div class="flex space-x-2">
                                        <input wire:model="search" type="text" placeholder="Cari produk..."
                                            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">

                                        <select wire:model="categoryFilter" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                            <option value="">Semua Kategori</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                    @foreach($products as $product)
                                        <div wire:click="productSelected({{ $product->id }})"
                                            class="bg-white p-3 rounded-lg shadow cursor-pointer hover:bg-gray-100 transition duration-200">
                                            <h4 class="font-medium text-gray-800">{{ $product->name }}</h4>
                                            <p class="text-gray-600 text-sm">Stok: {{ $product->stock }}</p>
                                            <p class="text-indigo-600 font-bold mt-1">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-4">
                                    {{ $products->links() }}
                                </div>
                            </div>
                        </div>

                        <!-- Shopping Cart Section -->
                        <div class="md:col-span-1">
                            <div class="bg-gray-50 p-4 rounded-lg shadow">
                                <h3 class="text-lg font-medium mb-4">Keranjang Belanja</h3>

                                @if(count($cart) > 0)
                                    <div class="space-y-3 mb-4 max-h-96 overflow-y-auto">
                                        @foreach($cart as $itemId => $item)
                                            <div class="bg-white p-3 rounded shadow-sm">
                                                <div class="flex justify-between">
                                                    <h4 class="font-medium">{{ $item['name'] }}</h4>
                                                    <button wire:click="removeItem({{ $itemId }})" class="text-red-500 hover:text-red-700">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                                <div class="flex justify-between items-center mt-2">
                                                    <div class="flex items-center space-x-2">
                                                        <button wire:click="decreaseQuantity({{ $itemId }})" class="bg-gray-200 px-2 py-1 rounded">-</button>
                                                        <span>{{ $item['quantity'] }}</span>
                                                        <button wire:click="increaseQuantity({{ $itemId }})" class="bg-gray-200 px-2 py-1 rounded">+</button>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="text-sm text-gray-600">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                                        <p class="font-bold">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="border-t border-gray-200 pt-4 mt-4">
                                        <div class="flex justify-between text-lg font-bold">
                                            <span>Total:</span>
                                            <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                                        </div>

                                        <button wire:click="showPayment" class="w-full mt-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                            Proses Pembayaran
                                        </button>
                                    </div>
                                @else
                                    <div class="bg-white p-4 rounded-lg text-center">
                                        <p class="text-gray-500">Keranjang belanja kosong</p>
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
                                <input type="number" id="cashAmount" wire:model="cashAmount" wire:keyup="calculateChange"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
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

                    {{-- <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="processPayment" type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
                            {{ $cashAmount < $total ? 'disabled' : '' }}>
                            Bayar
                        </button>
                        <button wire:click="$set('showPaymentModal', false)" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div> --}}
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <!-- Tombol Bayar -->
                        <button wire:click="processPayment" type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
                            wire:loading.attr="disabled" @disabled($cashAmount < $total)>
                            Bayar
                        </button>

                        <!-- Indikator Loading -->
                        <div wire:loading wire:target="processPayment" class="mt-2 text-gray-500 text-sm">
                            Memproses pembayaran...
                        </div>

                        <!-- Tombol Batal -->
                        <button wire:click="cancelPayment" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif

 @if(session()->has('cart_empty'))
    <script>
        alert("Keranjang kosong! Tambahkan produk sebelum membayar.");
    </script>
@endif


</div>
