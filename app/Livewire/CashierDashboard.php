<?php

namespace App\Livewire;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;


class CashierDashboard extends Component
{
    use WithPagination;

    public $cart = [];
    public $total = 0;
    public $cashAmount = 0;
    public $changeAmount = 0;
    public $search = '';
    public $categoryFilter = '';
    public $showPaymentModal = false;
    public $transactionCompleted = false;
    public $currentTransaction = null;

    protected $listeners = ['productSelected'];

    public function mount()
    {
        $this->resetCart();
    }

    public function resetCart()
    {
        $this->cart = [];
        $this->total = 0;
        $this->cashAmount = 0;
        $this->changeAmount = 0;
        $this->transactionCompleted = false;
        $this->currentTransaction = null;
    }

    public function productSelected($productId)
    {
        $product = Product::find($productId);

        if (!$product || $product->stock <= 0) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => 'Produk tidak tersedia']);
            return;
        }

        if (isset($this->cart[$productId]) && ($this->cart[$productId]['quantity'] + 1) > $product->stock) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => 'Stok tidak mencukupi']);
            return;
        }

        $this->updateCart($productId, 1);
    }

    public function increaseQuantity($productId)
    {
        $this->updateCart($productId, 1);
    }

    public function decreaseQuantity($productId)
    {
        $this->updateCart($productId, -1);
    }

    private function updateCart($productId, $amount)
    {
        $product = Product::find($productId);

        if (!$product)
            return;

        if (!isset($this->cart[$productId])) {
            if ($amount > 0) {
                $this->cart[$productId] = [
                    'id'       => $product->id,
                    'name'     => $product->name,
                    'price'    => $product->price,
                    'quantity' => $amount,
                    'subtotal' => $product->price * $amount
                ];
            }
        } else {
            $newQuantity = $this->cart[$productId]['quantity'] + $amount;

            if ($newQuantity <= 0) {
                unset($this->cart[$productId]);
            } elseif ($newQuantity <= $product->stock) {
                $this->cart[$productId]['quantity'] = $newQuantity;
                $this->cart[$productId]['subtotal'] = $newQuantity * $this->cart[$productId]['price'];
            } else {
                session()->flash('error', 'Not enough stock available');
            }
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

    public function calculateChange()
    {
        // Pastikan bahwa cashAmount adalah angka dengan konversi ke float atau int
        $this->cashAmount = is_numeric($this->cashAmount) ? (float) $this->cashAmount : 0;

        // Pastikan total juga numerik sebelum pengurangan
        $this->changeAmount = $this->cashAmount - (float) $this->total;
    }


    public function showPayment()
    {
        if (empty($this->cart)) {
            session()->flash('cart_empty', true);
            return;
        }
        $this->showPaymentModal = true;
    }


    public function processPayment()
    {
        if ($this->cashAmount < $this->total) {
            session()->flash('error', 'Cash amount is less than total');
            return;
        }

        $transaction = Transaction::create([
            'user_id'        => Auth::id(),
            'code'           => Transaction::generateCode(),
            'total_amount'   => $this->total,
            'payment_amount' => $this->cashAmount,
            'change_amount'  => $this->changeAmount,
            'status'         => 'completed',
        ]);

        foreach ($this->cart as $item) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id'     => $item['id'],
                'quantity'       => $item['quantity'],
                'price'          => $item['price'],
                'subtotal'       => $item['subtotal'],
            ]);

            Product::where('id', $item['id'])->decrement('stock', $item['quantity']);
        }

        $this->currentTransaction = $transaction->id;
        $this->transactionCompleted = true;
        $this->showPaymentModal = false;
        $this->showSuccessModal = true;
    }

    public $showSuccessModal = false;

    public function printReceipt()
    {
        return redirect()->route('receipt.download', ['transaction' => $this->currentTransaction]);
    }

    public function newTransaction()
    {
        $this->resetCart();
    }

    public function cancelPayment()
    {
        $this->resetCart();
        $this->showPaymentModal = false;
        $this->showSuccessModal = false;
        session()->flash('message', 'Pembayaran dibatalkan');
    }


    public function render()
    {
        $products = Product::when($this->search, fn($query) =>
            $query->where('name', 'like', '%' . $this->search . '%'))
            ->when($this->categoryFilter, fn($query) =>
                $query->where('category_id', $this->categoryFilter))
            ->where('stock', '>', 0)
            ->paginate(12);

        return view('livewire.cashier-dashboard', [
            'products'   => $products,
            'categories' => Category::all(),
        ]);
    }
}
