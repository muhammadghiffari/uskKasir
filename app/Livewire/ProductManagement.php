<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductManagement extends Component
{
    use WithPagination;

    public $name;
    public $description;
    public $price;
    public $stock;
    public $category_id;
    public $search = '';
    public $selectedProduct = null;
    public $isModalOpen = false;
    public $modalType = 'create'; // 'create' or 'edit'

    protected $rules = [
        'name'        => 'required|string|max:255',
        'description' => 'nullable|string',
        'price'       => 'required|numeric|min:0',
        'stock'       => 'required|integer|min:0',
        'category_id' => 'required|exists:categories,id',
    ];

    public function render()
    {
        return view('livewire.product-management', [
            'products'   => Product::where('name', 'like', '%' . $this->search . '%')
                ->paginate(10),
            'categories' => Category::all(),
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->modalType = 'create';
        $this->openModal();
    }

    public function store()
    {
        $this->validate();

        Product::create([
            'name'        => $this->name,
            'description' => $this->description,
            'price'       => $this->price,
            'stock'       => $this->stock,
            'category_id' => $this->category_id,
        ]);

        session()->flash('message', 'Produk berhasil ditambahkan.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $this->selectedProduct = $product->id;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->category_id = $product->category_id;

        $this->modalType = 'edit';
        $this->openModal();
    }

    public function update()
    {
        $this->validate();

        if ($this->selectedProduct) {
            $product = Product::find($this->selectedProduct);
            $product->update([
                'name'        => $this->name,
                'description' => $this->description,
                'price'       => $this->price,
                'stock'       => $this->stock,
                'category_id' => $this->category_id,
            ]);
            session()->flash('message', 'Produk berhasil diperbarui.');
            $this->closeModal();
            $this->resetInputFields();
        }
    }

    public function delete($id)
    {
        Product::find($id)->delete();
        session()->flash('message', 'Produk berhasil dihapus.');
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->description = '';
        $this->price = '';
        $this->stock = '';
        $this->category_id = '';
        $this->selectedProduct = null;
    }
}
