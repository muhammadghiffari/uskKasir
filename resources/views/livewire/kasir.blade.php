<div>
    <h2 class="text-xl font-bold">Kasir</h2>

    <div class="grid grid-cols-3 gap-4 mt-4">
        @foreach($products as $product)
            <div class="border p-2 text-center">
                <p>{{ $product->name }}</p>
                <p>Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                <button wire:click="addToCart({{ $product->id }})"
                    class="bg-blue-500 text-white p-1 rounded">Tambah</button>
            </div>
        @endforeach
    </div>

    <h3 class="text-lg font-bold mt-4">Keranjang</h3>
    <table class="w-full border mt-2">
        <thead>
            <tr class="border">
                <th>Nama</th>
                <th>Harga</th>
                <th>Qty</th>
                <th>Subtotal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cart as $productId => $item)
                <tr class="border">
                    <td>{{ $item['name'] }}</td>
                    <td>Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                    <td>
                        <button wire:click="removeItem({{ $productId }})"
                            class="bg-red-500 text-white p-1 rounded">Hapus</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="mt-2">Total: <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></p>

    <input type="number" wire:model="payment" placeholder="Masukkan pembayaran" class="border p-2 w-full mt-2" />
    <p class="mt-2">Kembalian: <strong>Rp {{ number_format($change, 0, ',', '.') }}</strong></p>

    <button wire:click="processTransaction" class="bg-green-500 text-white p-2 rounded mt-2 w-full">Bayar</button>
</div>
