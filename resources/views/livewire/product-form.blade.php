<div class="bg-white shadow-lg rounded-lg p-6">
    <h3 class="text-xl font-semibold mb-4">{{ $isEditing ? 'Edit Produk' : 'Tambah Produk Baru' }}</h3>

    <form wire:submit.prevent="save" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Nama Produk -->
            <div>
                <label for="name" class="block font-medium text-gray-700">Nama Produk <span class="text-red-500">*</span></label>
                <input type="text" wire:model="name" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan nama produk">
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Kategori -->
            <div>
                <label for="category_id" class="block font-medium text-gray-700">Kategori <span class="text-red-500">*</span></label>
                <select wire:model="category_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Harga -->
            <div>
                <label for="price" class="block font-medium text-gray-700">Harga <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                    <input type="number" wire:model="price" class="w-full pl-10 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="0">
                </div>
                @error('price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Stok -->
            <div>
                <label for="stock" class="block font-medium text-gray-700">Stok <span class="text-red-500">*</span></label>
                <input type="number" wire:model="stock" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="0">
                @error('stock') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Barcode -->
        <div>
            <label for="barcode" class="block font-medium text-gray-700">Barcode / SKU</label>
            <input type="text" wire:model="barcode" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan barcode produk">
            @error('barcode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Deskripsi -->
        <div>
            <label for="description" class="block font-medium text-gray-700">Deskripsi</label>
            <textarea wire:model="description" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" rows="3" placeholder="Deskripsi produk (opsional)"></textarea>
            @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Gambar Produk -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="image" class="block font-medium text-gray-700">Gambar Produk</label>
                <input type="file" wire:model="image" class="w-full border-gray-300 rounded-lg shadow-sm">
                <div wire:loading wire:target="image" class="text-sm text-gray-500 mt-1">Mengupload...</div>
                @error('image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Preview Gambar -->
            <div class="flex items-center">
                @if ($image)
                    <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="h-20 w-20 object-cover rounded-lg border">
                @elseif ($isEditing && isset($product) && $product->image)
                    <img src="{{ Storage::url($product->image) }}" alt="{{ $name }}" class="h-20 w-20 object-cover rounded-lg border">
                @endif
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex justify-between items-center mt-4">
            <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-800">Kembali</a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md">
                <i class="fas fa-save"></i> {{ $isEditing ? 'Update Produk' : 'Simpan Produk' }}
            </button>
        </div>

        <!-- Alert -->
        @if (session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded-lg mt-4">
                {{ session('success') }}
            </div>
        @elseif (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded-lg mt-4">
                {{ session('error') }}
            </div>
        @endif
    </form>
</div>
