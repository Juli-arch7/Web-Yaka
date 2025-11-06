@extends('layouts.app')

@section('title', 'Produk - Yaka Store')

@section('content')
<div class="flex flex-col lg:flex-row gap-8">
    <!-- Sidebar Filter -->
    <aside class="lg:w-64">
        <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
            <h3 class="font-bold text-lg mb-4">Filter Produk</h3>
            
            <!-- Search -->
            <form method="GET" action="{{ route('products.index') }}" class="mb-6">
                <input type="text" name="search" placeholder="Cari produk..." 
                       value="{{ request('search') }}"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="w-full mt-2 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
                    Cari
                </button>
            </form>

            <!-- Categories -->
            <div>
                <h4 class="font-semibold mb-3">Kategori</h4>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('products.index') }}" 
                           class="block hover:text-blue-600 {{ !request('category') ? 'text-blue-600 font-semibold' : '' }}">
                            Semua Produk
                        </a>
                    </li>
                    @foreach($categories as $category)
                    <li>
                        <a href="{{ route('products.index', ['category' => $category->slug]) }}" 
                           class="block hover:text-blue-600 {{ request('category') == $category->slug ? 'text-blue-600 font-semibold' : '' }}">
                            {{ $category->name }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </aside>

    <!-- Products Grid -->
    <div class="flex-1">
        <div class="mb-6">
            <h1 class="text-3xl font-bold">
                @if(request('category'))
                    {{ $categories->where('slug', request('category'))->first()->name ?? 'Produk' }}
                @else
                    Semua Produk
                @endif
            </h1>
            <p class="text-gray-600">{{ $products->total() }} produk ditemukan</p>
        </div>

        @if($products->isEmpty())
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <i class="fas fa-box-open text-6xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-semibold mb-2">Produk tidak ditemukan</h3>
            <p class="text-gray-600">Coba kata kunci atau filter lain</p>
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $product->description }}</p>
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

        <!-- Pagination -->
        <div class="mt-8">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>
@endsection