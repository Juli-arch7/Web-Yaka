<!-- Snippet untuk ditambahkan di resources/views/orders/show.blade.php -->

<!-- Bagian untuk Payment Status -->
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

<!-- Script untuk Payment -->
@push('scripts')
    @if ($order->status === 'pending')
        <script src="https://app.sandbox.midtrans.com/snap/snap.js"
                data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
        <script>
            document.getElementById('pay-button').addEventListener('click', function() {
                // Disable button saat loading
                this.disabled = true;
                this.textContent = 'Memproses...';

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
                                console.log('Payment success:', result);
                                // Optional: Reload halaman atau redirect
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            },
                            onPending: function(result) {
                                console.log('Payment pending:', result);
                                location.reload();
                            },
                            onError: function(result) {
                                console.log('Payment error:', result);
                                location.reload();
                            },
                            onClose: function() {
                                console.log('Payment dialog closed');
                                // Enable button lagi
                                document.getElementById('pay-button').disabled = false;
                                document.getElementById('pay-button').textContent = 'Bayar Sekarang';
                            }
                        });
                    } else {
                        alert('Gagal membuat pembayaran: ' + data.message);
                        document.getElementById('pay-button').disabled = false;
                        document.getElementById('pay-button').textContent = 'Bayar Sekarang';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat membuat pembayaran');
                    document.getElementById('pay-button').disabled = false;
                    document.getElementById('pay-button').textContent = 'Bayar Sekarang';
                });
            });
        </script>
    @endif
@endpush
