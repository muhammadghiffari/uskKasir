@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">Edit Produk</h1>

                @livewire('product-form', ['product_id' => $product_id])
            </div>
        </div>
    </div>
@endsection
