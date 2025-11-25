@extends('layouts.app')

@section('title', 'Invoice - ' . $order->invoice_no)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8">
        <!-- Header -->
        <div class="flex justify-between items-start mb-8 pb-6 border-b">
            <div>
                <h1 class="text-3xl font-bold mb-2">INVOICE</h1>
                <p class="text-gray-600">{{ $order->invoice_no }}</p>
            </div>
            <div class="text-right">
                @if($order->status === 'pending')
                <span class="inline-block px-4 py-2 bg-yellow-100 text-yellow-800 font-semibold rounded">
                    Menunggu Pembayaran
                </span>
                @elseif($order->status === 'processing')
                <span class="inline-block px-4 py-2 bg-blue-100 text-blue-800 font-semibold rounded">
                    Diproses
                </span>
                @elseif($order->status === 'completed')
                <span class="inline-block px-4 py-2 bg-green-100 text-green-800 font-semibold rounded">
                    Selesai
                </span>
                @else
                <span class="inline-block px-4 py-2 bg-red-100 text-red-800 font-semibold rounded">
                    Dibatalkan
                </span>
                @endif
            </div>
        </div>

        <!-- Order Info -->
        <div class="grid grid-cols-2 gap-8 mb-8">
            <div>
                <h3 class="font-bold mb-2">Informasi Pembeli</h3>
                <p class="text-gray-700">{{ $order->user->username }}</p>
                <p class="text-gray-700">{{ $order->user->email }}</p>
                <p class="text-gray-700">{{ $order->user->phone }}</p>
            </div>
            <div>
                <h3 class="font-bold mb-2">Alamat Pengiriman</h3>
                <p class="text-gray-700">{{ $order->shipping_address }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-8 mb-8">
            <div>
                <h3 class="font-bold mb-2">Tanggal Pesanan</h3>
                <p class="text-gray-700">{{ $order->created_at->format('d F Y, H:i') }}</p>
            </div>
            <div>
                <h3 class="font-bold mb-2">Metode Pembayaran</h3>
                <p class="text-gray-700">{{ $order->payment_method }}</p>
            </div>
        </div>

        <!-- Order Items -->
        <div class="mb-8">
            <h3 class="font-bold text-lg mb-4">Detail Pesanan</h3>
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-3">Produk</th>
                        <th class="text-center py-3">Harga</th>
                        <th class="text-center py-3">Jumlah</th>
                        <th class="text-right py-3">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr class="border-b">
                        <td class="py-4">
                            <div class="flex items-center gap-4">
                                @if($item->product->primaryImage)
                                <img src="{{ asset('storage/' . $item->product->primaryImage->image_path) }}" 
                                     alt="{{ $item->product_name }}"
                                     class="w-16 h-16 object-cover rounded">
                                @endif
                                <div>
                                    <p class="font-semibold">{{ $item->product_name }}</p>
                                    @if($item->variant)
                                    <p class="text-sm text-gray-600">
                                        {{ $item->variant->size }} - {{ $item->variant->color }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="text-center">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="flex justify-end">
            <div class="w-80">
                <div class="flex justify-between py-2">
                    <span class="text-gray-600">Subtotal</span>
                    <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600">Ongkir ({{ $order->shipping_method }})</span>
                    <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                </div>
                @if($order->discount > 0)
                <div class="flex justify-between py-2 text-green-600">
                    <span>Diskon</span>
                    <span>- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between py-4 border-t font-bold text-lg">
                    <span>Total</span>
                    <span class="text-blue-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        @if ($order->status === 'pending')
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                <p class="font-bold">Pembayaran Belum Diterima</p>
                <p>Silakan lakukan pembayaran untuk mengkonfirmasi pesanan Anda.</p>
            </div>

            <button 
                type="button"
                id="pay-button"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
            >
                Bayar Sekarang
            </button>
        @elseif ($order->status === 'paid')
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <p class="font-bold">Pembayaran Berhasil</p>
                <p>Pesanan Anda telah dikonfirmasi. Terima kasih!</p>
            </div>
        @endif

        @push('scripts')
            @if ($order->status === 'pending')
                <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
                <script>
                    console.log('[Payment] Script loaded');
                    console.log('[Payment] Order ID:', {{ $order->id }});
                    console.log('[Payment] Client Key set:', !!('{{ config('midtrans.client_key') }}'));
                    
                    // Find button immediately
                    const payButton = document.getElementById('pay-button');
                    console.log('[Payment] Button found:', !!payButton);
                    
                    if (payButton) {
                        // Click handler
                        payButton.onclick = function() {
                            console.log('[Payment] ▶ BUTTON CLICKED');
                            
                            payButton.disabled = true;
                            payButton.textContent = 'Memproses...';
                            
                            const url = '{{ route("payment.create", $order->id) }}';
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                            
                            console.log('[Payment] Fetching:', url);
                            console.log('[Payment] CSRF Token present:', !!csrfToken);
                            
                            fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => {
                                console.log('[Payment] Status:', response.status);
                                return response.json();
                            })
                            .then(data => {
                                console.log('[Payment] Response:', data);
                                
                                if (data.success && data.snap_token) {
                                    console.log('[Payment] ▶ SNAP AVAILABLE:', typeof snap);
                                    
                                    // Double check snap exists
                                    if (typeof window.snap === 'undefined') {
                                        console.error('[Payment] ✗ window.snap is undefined!');
                                        alert('Error: Snap SDK tidak ter-load');
                                        payButton.disabled = false;
                                        payButton.textContent = 'Bayar Sekarang';
                                        return;
                                    }
                                    
                                    console.log('[Payment] ▶ OPENING SNAP...');
                                    
                                    window.snap.pay(data.snap_token, {
                                        onSuccess: function(result) {
                                            console.log('[Payment] ✓ SUCCESS');
                                            alert('Pembayaran berhasil!');
                                            location.reload();
                                        },
                                        onPending: function(result) {
                                            console.log('[Payment] ⏳ PENDING');
                                            alert('Pembayaran sedang diproses');
                                            location.reload();
                                        },
                                        onError: function(result) {
                                            console.log('[Payment] ✗ ERROR:', result);
                                            alert('Pembayaran gagal');
                                            payButton.disabled = false;
                                            payButton.textContent = 'Bayar Sekarang';
                                        },
                                        onClose: function() {
                                            console.log('[Payment] CLOSED');
                                            payButton.disabled = false;
                                            payButton.textContent = 'Bayar Sekarang';
                                        }
                                    });
                                } else {
                                    console.error('[Payment] ✗ No snap token:', data);
                                    alert('Error: ' + (data.message || 'Gagal mendapatkan token'));
                                    payButton.disabled = false;
                                    payButton.textContent = 'Bayar Sekarang';
                                }
                            })
                            .catch(error => {
                                console.error('[Payment] ✗ FETCH ERROR:', error);
                                alert('Error: ' + error.message);
                                payButton.disabled = false;
                                payButton.textContent = 'Bayar Sekarang';
                            });
                        };
                        
                        console.log('[Payment] ✓ Click handler attached');
                    }
                </script>
            @endif
        @endpush


        <!-- Actions -->
        <div class="flex justify-between items-center mt-8 pt-6 border-t">
            <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-700">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Pesanan
            </a>
            <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-print mr-2"></i> Cetak Invoice
            </button>
        </div>
    </div>
</div>
@endsection