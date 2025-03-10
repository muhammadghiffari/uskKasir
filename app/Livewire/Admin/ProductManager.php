<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class ProductManager extends Component
{
    use WithPagination;
    use WithFileUploads;

    // Form properties
    public $name;
    public $category_id;
    public $description;
    public $price;
    public $stock;

    // Edit mode
    public $editMode = false;
    public $productId;

    // Filters
    public $search = '';
    public $categoryFilter = '';

    // Modals
    public $showDeleteModal = false;
    public $showFormModal = false;
    public $productToDelete;

    protected $rules = [
        'name' => 'required|min:3',
        'category_id' => 'required|exists:categories,id',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'description' => 'nullable|string',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset(['name', 'category_id', 'description', 'price', 'stock', 'productId']);
        $this->editMode = false;
        $this->showFormModal = true;
    }

    public function openEditModal($productId)
    {
        $this->resetValidation();
        $this->productId = $productId;
        $product = Product::findOrFail($productId);

        $this->name = $product->name;
        $this->category_id = $product->category_id;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->stock = $product->stock;

        $this->editMode = true;
        $this->showFormModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->editMode) {
            $product = Product::findOrFail($this->productId);
            $product->update([
                'name' => $this->name,
                'category_id' => $this->category_id,
                'description' => $this->description,
                'price' => $this->price,
                'stock' => $this->stock,
            ]);

            session()->flash('message', 'Product successfully updated.');
        } else {
            Product::create([
                'name' => $this->name,
                'category_id' => $this->category_id,
                'description' => $this->description,
                'price' => $this->price,
                'stock' => $this->stock,
            ]);

            session()->flash('message', 'Product successfully created.');
        }

        $this->showFormModal = false;
        $this->reset(['name', 'category_id', 'description', 'price', 'stock', 'productId']);
    }

    public function confirmDelete($productId)
    {
        $this->productToDelete = $productId;
        $this->showDeleteModal = true;
    }

    public function deleteProduct()
    {
        $product = Product::findOrFail($this->productToDelete);
        $product->delete();

        session()->flash('message', 'Product successfully deleted.');
        $this->showDeleteModal = false;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
    }

    public function render()
    {
        $products = Product::query()
            ->when($this->search, function($query) {
                return $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->categoryFilter, function($query) {
                return $query->where('category_id', $this->categoryFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $categories = Category::all();

        return view('livewire.admin.product-manager', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
