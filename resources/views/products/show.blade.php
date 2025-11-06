@extends('layouts.app')

@section('title', $product->name . ' - Yaka Store')

@section('content')
<div class="bg-white rounded-lg shadow-md p-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Product Images -->
        <div>
            @if($product->images->count() > 0)
            <div class="mb-4">
                <img id="mainImage" src="{{ asset('storage/' . $product->primaryImage->image_path) }}" 
                     alt="{{ $product->name }}" 
                     class="w-full h-96 object-cover rounded-lg">
            </div>
            <div class="grid grid-cols-4 gap-2">
                @foreach($product->images as $image)
                <img src="{{ asset('storage/' . $image->image_path) }}" 
                     alt="{{ $product->name }}"
                     class="w-full h-24 object-cover rounded cursor-pointer hover:opacity-75"
                     onclick="document.getElementById('mainImage').src=this.src">
                @endforeach
            </div>
            @else
            <div class="w-full h-96 bg-gray-200 rounded-lg flex items-center justify-center">
                <i class="fas fa-image text-gray-400 text-6xl"></i>
            </div>
            @endif
        </div>

        <!-- Product Info -->
        <div>
            <p class="text-gray-600 mb-2">{{ $product->category->name }}</p>
            <h1 class="text-3xl font-bold mb-4">{{ $product->name }}</h1>
            
            <div class="flex items-center mb-4">
                <div class="flex items-center text-yellow-500 mr-2">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= ($product->averageRating() ?? 0))
                        <i class="fas fa-star"></i>
                        @else
                        <i class="far fa-star"></i>
                        @endif
                    @endfor
                </div>
                <span class="text-gray-600">({{ $product->reviews->count() }} ulasan)</span>
            </div>

            <div class="text-4xl font-bold text-blue-600 mb-6">
                Rp {{ number_format($product->price, 0, ',', '.') }}
            </div>

            <div class="mb-6">
                <h3 class="font-semibold mb-2">Deskripsi</h3>
                <p class="text-gray-700">{{ $product->description }}</p>
            </div>

            @if($product->details)
            <div class="mb-6">
                <h3 class="font-semibold mb-2">Rincian Produk</h3>
                <p class="text-gray-700">{{ $product->details }}</p>
            </div>
            @endif

            @auth
            <form method="POST" action="{{ route('cart.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                @if($product->variants->count() > 0)
                <!-- Size Selection -->
                <div>
                    <label class="block font-semibold mb-2">Ukuran</label>
                    <div class="flex gap-2">
                        @foreach($product->variants->unique('size') as $variant)
                        <label class="cursor-pointer">
                            <input type="radio" name="size" value="{{ $variant->size }}" class="hidden peer" required>
                            <div class="px-4 py-2 border rounded-lg peer-checked:border-blue-600 peer-checked:bg-blue-50 hover:border-gray-400">
                                {{ $variant->size }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Color Selection -->
                <div>
                    <label class="block font-semibold mb-2">Warna</label>
                    <div class="flex gap-2">
                        @foreach($product->variants->unique('color') as $variant)
                        <label class="cursor-pointer">
                            <input type="radio" name="color" value="{{ $variant->color }}" class="hidden peer" required>
                            <div class="px-4 py-2 border rounded-lg peer-checked:border-blue-600 peer-checked:bg-blue-50 hover:border-gray-400">
                                {{ $variant->color }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Quantity -->
                <div>
                    <label class="block font-semibold mb-2">Jumlah</label>
                    <input type="number" name="quantity" value="1" min="1" max="10" 
                           class="w-24 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Add to Cart Button -->
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700">
                    <i class="fas fa-shopping-cart mr-2"></i> Masukkan Keranjang
                </button>
            </form>
            @else
            <a href="{{ route('login') }}" class="block w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 text-center">
                Login untuk Membeli
            </a>
            @endauth
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="mt-12 border-t pt-8">
        <h2 class="text-2xl font-bold mb-6">Penilaian & Ulasan</h2>

        @auth
        @if(!$product->reviews->where('user_id', auth()->id())->count())
        <form method="POST" action="{{ route('reviews.store', $product) }}" class="bg-gray-50 p-6 rounded-lg mb-6">
            @csrf
            <h3 class="font-semibold mb-4">Tulis Ulasan</h3>
            <div class="mb-4">
                <label class="block font-semibold mb-2">Rating</label>
                <div class="flex gap-2">
                    @for($i = 1; $i <= 5; $i++)
                    <label class="cursor-pointer">
                        <input type="radio" name="rating" value="{{ $i }}" class="hidden peer" required>
                        <i class="fas fa-star text-2xl text-gray-300 peer-checked:text-yellow-500"></i>
                    </label>
                    @endfor
                </div>
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-2">Komentar</label>
                <textarea name="comment" rows="4" 
                          class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Bagikan pengalaman Anda..."></textarea>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Kirim Ulasan
            </button>
        </form>
        @endif
        @endauth

        <!-- Reviews List -->
        <div class="space-y-4">
            @forelse($product->reviews as $review)
            <div class="border-b pb-4">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <span class="font-semibold">{{ $review->user->username }}</span>
                        <div class="flex items-center text-yellow-500 mt-1">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                <i class="fas fa-star"></i>
                                @else
                                <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                    </div>
                    <span class="text-sm text-gray-600">{{ $review->created_at->diffForHumans() }}</span>
                </div>
                @if($review->comment)
                <p class="text-gray-700">{{ $review->comment }}</p>
                @endif
            </div>
            @empty
            <p class="text-gray-600 text-center py-8">Belum ada ulasan</p>
            @endforelse
        </div>
    </div>
</div>
@endsection