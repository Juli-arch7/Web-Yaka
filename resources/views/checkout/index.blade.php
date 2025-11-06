@extends('layouts.app')

@section('title', 'Checkout - Yaka Store')

@section('content')
<h1 class="text-3xl font-bold mb-8">Checkout</h1>

<form method="POST" action="{{ route('checkout.store') }}">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Checkout Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-xl font-bold mb-4">Alamat Pengiriman</h3>
                <textarea name="shipping_address" 
                          rows="4" 
                          class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Masukkan alamat lengkap..."
                          required>{{ auth()->user()->address }}</textarea>
                @error('shipping_address')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-xl font-bold mb-4">Metode Pengiriman</h3>
                <div class="space-y-3">
                    <label class="flex items-center justify-between p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <div class="flex items-center">
                            <input type="radio" name="shipping_method" value="JNE REG" class="mr-3" required>
                            <div>
                                <p class="font-semibold">JNE REG</p>
                                <p class="text-sm text-gray-600">Estimasi 2-3 hari</p>
                            </div>
                        </div>
                        <span class="font-semibold">Rp 15.000</span>
                    </label>
                    <label class="flex items-center justify-between p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <div class="flex items-center">
                            <input type="radio" name="shipping_method" value="JNE YES" class="mr-3">
                            <div>
                                <p class="font-semibold">JNE YES</p>
                                <p class="text-sm text-gray-600">Estimasi 1 hari</p>
                            </div>
                        </div>
                        <span class="font-semibold">Rp 25.000</span>
                    </label>
                    <label class="flex items-center justify-between p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <div class="flex items-center">
                            <input type="radio" name="shipping_method" value="Sicepat REG" class="mr-3">
                            <div>
                                <p class="font-semibold">Sicepat REG</p>
                                <p class="text-sm text-gray-600">Estimasi 2-3 hari</p>
                            </div>
                        </div>
                        <span class="font-semibold">Rp 12.000</span>
                    </label>
                </div>
                <input type="hidden" name="shipping_cost" id="shipping_cost" value="15000">
                @error('shipping_method')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold mb-4">Metode Pembayaran</h3>
                <div class="space-y-3">
                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_method" value="Transfer Bank" class="mr-3" required>
                        <div>
                            <p class="font-semibold">Transfer Bank</p>
                            <p class="text-sm text-gray-600">BCA, BNI, Mandiri</p>
                        </div>
                    </label>
                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_method" value="E-Wallet" class="mr-3">
                        <div>
                            <p class="font-semibold">E-Wallet</p>
                            <p class="text-sm text-gray-600">GoPay, OVO, DANA</p>
                        </div>
                    </label>
                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_method" value="COD" class="mr-3">
                        <div>
                            <p class="font-semibold">COD (Cash on Delivery)</p>
                            <p class="text-sm text-gray-600">Bayar saat barang tiba</p>
                        </div>
                    </label>
                </div>
                @error('payment_method')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h3 class="text-xl font-bold mb-4">Ringkasan Pesanan</h3>
                
                <div class="space-y-3 mb-4 max-h-60 overflow-y-auto">
                    @foreach($carts as $cart)
                    <div class="flex gap-3">
                        @if($cart->product->primaryImage)
                        <img src="{{ asset('storage/' . $cart->product->primaryImage->image_path) }}" 
                             alt="{{ $cart->product->name }}"
                             class="w-16 h-16 object-cover rounded">
                        @endif
                        <div class="flex-1">
                            <p class="font-semibold text-sm">{{ $cart->product->name }}</p>
                            <p class="text-xs text-gray-600">{{ $cart->quantity }}x</p>
                            <p class="text-sm font-semibold text-blue-600">
                                Rp {{ number_format($cart->subtotal(), 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="border-t pt-4 space-y-2 mb-4">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Ongkir</span>
                        <span id="shipping_display">Rp 15.000</span>
                    </div>
                </div>

                <div class="border-t pt-4 mb-6">
                    <div class="flex justify-between font-bold text-lg">
                        <span>Total</span>
                        <span class="text-blue-600" id="total_display">Rp {{ number_format($subtotal + 15000, 0, ',', '.') }}</span>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700">
                    Buat Pesanan
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    // Update shipping cost when method changes
    document.querySelectorAll('input[name="shipping_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            let cost = 0;
            if (this.value === 'JNE REG') cost = 15000;
            else if (this.value === 'JNE YES') cost = 25000;
            else if (this.value === 'Sicepat REG') cost = 12000;
            
            document.getElementById('shipping_cost').value = cost;
            document.getElementById('shipping_display').textContent = 'Rp ' + cost.toLocaleString('id-ID');
            
            const subtotal = {{ $subtotal }};
            const total = subtotal + cost;
            document.getElementById('total_display').textContent = 'Rp ' + total.toLocaleString('id-ID');
        });
    });
</script>
@endsection