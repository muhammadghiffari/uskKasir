<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class ProductForm extends Component
{
    use WithFileUploads;

    public $name;
    public $description;
    public $price;
    public $stock;
    public $category_id;
    public $image;
    public $barcode;
    public $product_id;
    public $isEditing = false;

    protected $rules = [
        'name'        => 'required|min:3',
        'description' => 'nullable',
        'price'       => 'required|numeric|min:0',
        'stock'       => 'required|integer|min:0',
        'category_id' => 'required|exists:categories,id',
        'image'       => 'nullable|image|max:1024', // max 1MB
        'barcode'     => 'nullable|string|max:50',
    ];

    public function mount($product_id = null)
    {
        if ($product_id) {
            $this->product_id = $product_id;
            $this->loadProduct();
        }
    }

    public function loadProduct()
    {
        $product = Product::find($this->product_id);
        if ($product) {
            $this->name = $product->name;
            $this->description = $product->description;
            $this->price = $product->price;
            $this->stock = $product->stock;
            $this->category_id = $product->category_id;
            $this->barcode = $product->barcode;
            $this->isEditing = true;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->isEditing) {
                $product = Product::find($this->product_id);
            } else {
                $product = new Product();
            }

            $product->name = $this->name;
            $product->description = $this->description;
            $product->price = $this->price;
            $product->stock = $this->stock;
            $product->category_id = $this->category_id;
            $product->barcode = $this->barcode;

            // Handle image upload if provided
            if ($this->image) {
                $imageName = Str::slug($this->name) . '-' . time() . '.' . $this->image->extension();
                $this->image->storeAs('public/products', $imageName);
                $product->image = 'products/' . $imageName;
            }

            $product->save();

            session()->flash('success', $this->isEditing ? 'Produk berhasil diperbarui!' : 'Produk berhasil ditambahkan!');

            // Reset form after saving
            if (!$this->isEditing) {
                $this->reset(['name', 'description', 'price', 'stock', 'category_id', 'image', 'barcode']);
            }

            // Emit event to refresh product list if needed
            $this->dispatch('productAdded');

            return redirect()->route('products.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.product-form', [
            'categories' => Category::orderBy('name')->get()
        ]);
    }
}
