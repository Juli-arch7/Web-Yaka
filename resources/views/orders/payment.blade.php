@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Pembayaran Order</h1>

        @if ($order->status === 'paid')
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <p class="font-bold">Pembayaran Berhasil</p>
                <p>Order Anda telah dibayar. Terima kasih!</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Detail Order</h2>
                <div class="mb-4">
                    <p class="text-gray-600">Invoice No: <span class="font-semibold">{{ $order->invoice_no }}</span></p>
                    <p class="text-gray-600">Total: <span class="font-semibold text-lg">Rp {{ number_format($order->total, 0, ',', '.') }}</span></p>
                </div>

                <button 
                    id="pay-button"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                >
                    Bayar Sekarang
                </button>
            </div>
        @endif

        <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-800">
            ← Kembali ke Pesanan
        </a>
    </div>
</div>

@push('scripts')
    @if ($order->status !== 'paid')
        <script src="https://app.sandbox.midtrans.com/snap/snap.js"
                data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
        <script>
            document.getElementById('pay-button').onclick = function() {
                fetch('{{ route("payment.create", $order->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        snap.pay(data.snap_token, {
                            onSuccess: function(result) {
                                console.log('Success:', result);
                                window.location.href = '{{ route("payment.finish") }}?order_id={{ $order->id }}&status_code=' + result.status_code + '&transaction_status=' + result.transaction_status;
                            },
                            onPending: function(result) {
                                console.log('Pending:', result);
                                window.location.href = '{{ route("payment.pending") }}?order_id={{ $order->id }}&transaction_status=' + result.transaction_status;
                            },
                            onError: function(result) {
                                console.log('Error:', result);
                                window.location.href = '{{ route("payment.error") }}?order_id={{ $order->id }}';
                            }
                        });
                    } else {
                        alert('Gagal membuat pembayaran: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan');
                });
            };
        </script>
    @endif
@endpush
@endsection
