@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-semibold mb-4">Daftar Produk</h1>

        {{-- Tombol Tambah Produk --}}
        <a href="{{ route('products.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg mb-4 inline-block">Tambah Produk</a>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300 shadow-md rounded-lg">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left">Nama</th>
                        <th class="py-3 px-4 text-left">Harga</th>
                        <th class="py-3 px-4 text-left">Stok</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr class="border-b border-gray-300 hover:bg-gray-100">
                            <td class="py-3 px-4">{{ $product->name }}</td>
                            <td class="py-3 px-4">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            <td class="py-3 px-4">{{ $product->stock }}</td>
                            <td class="py-3 px-4 text-center">
                                {{-- Tombol Edit --}}
                                <a href="{{ route('products.edit', $product->id) }}"
                                    class="bg-yellow-500 text-white px-3 py-1 rounded-lg text-sm">Edit</a>

                                {{-- Tombol Hapus --}}
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-500 text-white px-3 py-1 rounded-lg text-sm"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
