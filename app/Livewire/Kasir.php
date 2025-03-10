<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Livewire\Component;

class Kasir extends Component
{
    public $cart = [];
    public $total = 0;
    public $payment = 0;
    public $change = 0;

    public function addToCart($productId)
    {
        $product = Product::findOrFail($productId);
        if (!isset($this->cart[$productId])) {
            $this->cart[$productId] = [
                'name'     => $product->name,
                'price'    => $product->price,
                'quantity' => 1,
                'subtotal' => $product->price
            ];
        } else {
            $this->cart[$productId]['quantity'] += 1;
            $this->cart[$productId]['subtotal'] = $this->cart[$productId]['quantity'] * $product->price;
        }
        $this->calculateTotal();
    }

    public function removeItem($productId)
    {
        unset($this->cart[$productId]);
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->total = array_sum(array_column($this->cart, 'subtotal'));
    }

    public function processTransaction()
    {
        if ($this->payment < $this->total) {
            session()->flash('error', 'Pembayaran kurang!');
            return;
        }

        $transaction = Transaction::create([
            'user_id'        => auth()->id(),
            'code'           => 'TRX' . time(),
            'total_amount'   => $this->total,
            'payment_amount' => $this->payment,
            'change_amount'  => $this->payment - $this->total,
            'status'         => 'completed'
        ]);

        foreach ($this->cart as $productId => $item) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id'     => $productId,
                'quantity'       => $item['quantity'],
                'price'          => $item['price'],
                'subtotal'       => $item['subtotal'],
            ]);
        }

        $this->reset(['cart', 'total', 'payment', 'change']);
        session()->flash('success', 'Transaksi berhasil!');
    }

    public function render()
    {
        return view('livewire.kasir', [
            'products' => Product::all()
        ]);
    }
}
