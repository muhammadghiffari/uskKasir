@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">Tambah Produk Baru</h1>

                @livewire('product-form')
            </div>
        </div>
    </div>
@endsection
