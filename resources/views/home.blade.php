@extends('layouts.app')

@section('title', 'Home - Yaka Store')

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg p-12 mb-12">
    <div class="max-w-2xl">
        <h1 class="text-5xl font-bold mb-4">Selamat Datang di Yaka Store</h1>
        <p class="text-xl mb-6">Temukan koleksi fashion terkini dengan kualitas terbaik dan harga terjangkau</p>
        <a href="{{ route('products.index') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 inline-block">
            Belanja Sekarang
        </a>
    </div>
</div>

<!-- Categories -->
<div class="mb-12">
    <h2 class="text-3xl font-bold mb-6">Kategori Produk</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach($categories as $category)
        <a href="{{ route('products.index', ['category' => $category->slug]) }}" 
           class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition text-center">
            <div class="text-4xl mb-2">👕</div>
            <h3 class="font-semibold">{{ $category->name }}</h3>
            <p class="text-sm text-gray-600">{{ $category->products_count }} produk</p>
        </a>
        @endforeach
    </div>
</div>

<!-- Featured Products -->
<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold">Produk Terbaru</h2>
        <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-700">
            Lihat Semua <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($products as $product)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
            <a href="{{ route('products.show', $product->slug) }}">
                @if($product->primaryImage)
                <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" 
                     alt="{{ $product->name }}" 
                     class="w-full h-64 object-cover">
                @else
                <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                    <i class="fas fa-image text-gray-400 text-4xl"></i>
                </div>
                @endif
            </a>
            <div class="p-4">
                <p class="text-sm text-gray-600 mb-1">{{ $product->category->name }}</p>
                <h3 class="font-semibold text-lg mb-2">
                    <a href="{{ route('products.show', $product->slug) }}" class="hover:text-blue-600">
                        {{ $product->name }}
                    </a>
                </h3>
                <div class="flex items-center justify-between">
                    <span class="text-xl font-bold text-blue-600">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                    <div class="flex items-center text-yellow-500">
                        <i class="fas fa-star"></i>
                        <span class="ml-1 text-gray-600">{{ number_format($product->averageRating() ?? 0, 1) }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection