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
use Livewire\Attributes\On;

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
    public $showSuccessModal = false;
    public $errorMessage = '';

    protected $listeners = ['productSelected'];

    // Define updatedSearch and updatedCategoryFilter to reset pagination
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }

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

    #[On('product-selected')]
    public function productSelected($productId)
    {
        $product = Product::find($productId);

        if (!$product || $product->stock <= 0) {
            $this->errorMessage = 'Produk tidak tersedia';
            $this->dispatch('show-alert', ['message' => $this->errorMessage]);
            return;
        }

        if (isset($this->cart[$productId]) && ($this->cart[$productId]['quantity'] + 1) > $product->stock) {
            $this->errorMessage = 'Stok tidak mencukupi';
            $this->dispatch('show-alert', ['message' => $this->errorMessage]);
            return;
        }

        $this->updateCart($productId, 1);
    }

    public function increaseQuantity($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            $this->errorMessage = 'Produk tidak ditemukan';
            return;
        }

        if (isset($this->cart[$productId]) && ($this->cart[$productId]['quantity'] + 1) > $product->stock) {
            session()->flash('error', 'Stok tidak mencukupi');
            return;
        }

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
                session()->flash('error', 'Stok tidak mencukupi');
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
            session()->flash('error', 'Jumlah uang kurang dari total belanja');
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
        $this->showPaymentModal = false;
        $this->showSuccessModal = false;
        session()->flash('message', 'Pembayaran dibatalkan');
    }

    public function render()
    {
        $products = Product::when($this->search !== '', function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%');
        })
            ->when($this->categoryFilter !== '', function ($query) {
                $query->where('category_id', $this->categoryFilter);
            })
            ->where('stock', '>', 0)
            ->paginate(12);

        return view('livewire.cashier-dashboard', [
            'products'   => $products,
            'categories' => Category::all(),
        ]);
    }
}
