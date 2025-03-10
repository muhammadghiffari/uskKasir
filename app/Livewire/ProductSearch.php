<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class ProductSearch extends Component
{
    public $search = '';
    public $products = [];

    public function updatedSearch()
    {
        if (strlen($this->search) >= 2) {
            $this->products = Product::where('name', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%')
                ->take(10)
                ->get();
        } else {
            $this->products = [];
        }
    }

    public function selectProduct($productId)
    {
        $product = Product::find($productId);
        $this->dispatch('productSelected', $product);
        $this->search = '';
        $this->products = [];
    }

    public function render()
    {
        return view('livewire.product-search');
    }
}
