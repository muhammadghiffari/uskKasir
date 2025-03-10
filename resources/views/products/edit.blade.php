@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-semibold mb-4">Edit Product</h1>

        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-lg p-6">
                <!-- Kategori -->
                <div class="mb-4">
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                    <select id="category_id" name="category_id" class="w-full border-gray-300 rounded-lg p-2">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ $category->id == $product->category_id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Nama Produk -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Product Name</label>
                    <input type="text" id="name" name="name" value="{{ $product->name }}" required
                        class="w-full border-gray-300 rounded-lg p-2">
                </div>

                <!-- Deskripsi -->
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="description" name="description" rows="3"
                        class="w-full border-gray-300 rounded-lg p-2">{{ $product->description }}</textarea>
                </div>

                <!-- Harga -->
                <div class="mb-4">
                    <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                    <input type="number" id="price" name="price" value="{{ $product->price }}" required
                        class="w-full border-gray-300 rounded-lg p-2">
                </div>

                <!-- Stok -->
                <div class="mb-4">
                    <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
                    <input type="number" id="stock" name="stock" value="{{ $product->stock }}" required
                        class="w-full border-gray-300 rounded-lg p-2">
                </div>

                <!-- Submit Button -->
                <button type="submit" class="bg-blue-500 text-black px-4 py-2 rounded-lg">
                    Update
                </button>
            </div>
        </form>
    </div>
@endsection
