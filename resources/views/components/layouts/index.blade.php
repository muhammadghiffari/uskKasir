@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">Manajemen Produk</h1>

                @livewire('product-list')
            </div>
        </div>
    </div>
@endsection
