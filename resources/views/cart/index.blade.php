@extends('layouts.app')

@section('title', 'Keranjang Belanja - Yaka Store')

@section('content')
<h1 class="text-3xl font-bold mb-8">Keranjang Belanja</h1>

@if($carts->isEmpty())
<div class="bg-white rounded-lg shadow-md p-12 text-center">
    <i class="fas fa-shopping-cart text-6xl text-gray-400 mb-4"></i>
    <h3 class="text-xl font-semibold mb-2">Keranjang Anda Kosong</h3>
    <p class="text-gray-600 mb-6">Mulai berbelanja sekarang!</p>
    <a href="{{ route('products.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 inline-block">
        Belanja Sekarang
    </a>
</div>
@else
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Cart Items -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md">
            @foreach($carts as $cart)
            <div class="flex items-center gap-4 p-6 border-b last:border-b-0">
                @if($cart->product->primaryImage)
                <img src="{{ asset('storage/' . $cart->product->primaryImage->image_path) }}" 
                     alt="{{ $cart->product->name }}"
                     class="w-24 h-24 object-cover rounded">
                @else
                <div class="w-24 h-24 bg-gray-200 rounded flex items-center justify-center">
                    <i class="fas fa-image text-gray-400"></i>
                </div>
                @endif

                <div class="flex-1">
                    <h3 class="font-semibold text-lg">{{ $cart->product->name }}</h3>
                    @if($cart->variant)
                    <p class="text-sm text-gray-600">
                        {{ $cart->variant->size }} - {{ $cart->variant->color }}
                    </p>
                    @endif
                    <p class="text-blue-600 font-semibold mt-1">
                        Rp {{ number_format($cart->product->price, 0, ',', '.') }}
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Quantity -->
                    <form method="POST" action="{{ route('cart.update', $cart) }}" class="flex items-center gap-2">
                        @csrf
                        @method('PUT')
                        <input type="number" 
                               name="quantity" 
                               value="{{ $cart->quantity }}" 
                               min="1" 
                               max="10"
                               class="w-16 px-2 py-1 border rounded text-center"
                               onchange="this.form.submit()">
                    </form>

                    <!-- Subtotal -->
                    <div class="text-right w-32">
                        <p class="font-bold text-lg">
                            Rp {{ number_format($cart->subtotal(), 0, ',', '.') }}
                        </p>
                    </div>

                    <!-- Delete -->
                    <form method="POST" action="{{ route('cart.destroy', $cart) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Order Summary -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
            <h3 class="text-xl font-bold mb-4">Ringkasan Belanja</h3>
            
            <div class="space-y-2 mb-4">
                <div class="flex justify-between">
                    <span>Subtotal ({{ $carts->count() }} item)</span>
                    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Ongkir</span>
                    <span>Dihitung di checkout</span>
                </div>
            </div>

            <div class="border-t pt-4 mb-6">
                <div class="flex justify-between font-bold text-lg">
                    <span>Total</span>
                    <span class="text-blue-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>

            <a href="{{ route('checkout.index') }}" class="block w-full bg-blue-600 text-white py-3 rounded-lg font-semibold text-center hover:bg-blue-700">
                Checkout
            </a>

            <a href="{{ route('products.index') }}" class="block w-full text-center text-blue-600 mt-4 hover:text-blue-700">
                <i class="fas fa-arrow-left mr-2"></i> Lanjut Belanja
            </a>
        </div>
    </div>
</div>
@endif
@endsection
