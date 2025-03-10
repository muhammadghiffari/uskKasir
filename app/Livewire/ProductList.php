<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';

    protected $listeners = ['productAdded' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function deleteProduct($id)
    {
        $product = Product::find($id);

        if ($product) {
            // Check if product is used in transactions
            $isUsed = $product->transactionItems()->exists();

            if ($isUsed) {
                session()->flash('error', 'Produk tidak dapat dihapus karena sudah digunakan dalam transaksi.');
                return;
            }

            // Delete product image if exists
            if ($product->image) {
                $imagePath = public_path('storage/' . $product->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $product->delete();
            session()->flash('success', 'Produk berhasil dihapus!');
        }
    }

    public function render()
    {
        $products = Product::when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('barcode', 'like', '%' . $this->search . '%');
        })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category_id', $this->categoryFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.product-list', [
            'products'   => $products,
            'categories' => Category::orderBy('name')->get()
        ]);
    }
}
