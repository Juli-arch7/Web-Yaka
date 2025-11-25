# Setup Midtrans di Laravel - Web Yaka

## Langkah-langkah Integrasi Midtrans

### 1. Package sudah terinstal
```bash
composer require midtrans/midtrans-php
```

### 2. File Konfigurasi
- `config/midtrans.php` - Konfigurasi Midtrans
- Pastikan `.env` memiliki:
```dotenv
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_IS_PRODUCTION=false  # Set true untuk production
```

### 3. Database Migration
Jalankan migration untuk membuat tabel payments:
```bash
php artisan migrate
```

### 4. Service & Controller
- `app/Services/MidtransService.php` - Service untuk handle Midtrans API
- `app/Http/Controllers/PaymentController.php` - Controller untuk payment flow

### 5. Routes
Payment routes sudah ditambahkan di `routes/web.php`:
- `POST /payment/create/{order}` - Create payment snap
- `POST /midtrans/notification` - Webhook untuk notifikasi Midtrans
- `POST /payment/finish` - Callback sukses
- `POST /payment/error` - Callback error
- `POST /payment/pending` - Callback pending

### 6. Model
- `app/Models/Payment.php` - Model untuk menyimpan data payment

### 7. View
- `resources/views/orders/payment.blade.php` - Halaman pembayaran

## Cara Penggunaan

### A. Dari Checkout
Setelah checkout, redirect ke halaman payment:
```php
return redirect()->route('orders.show', $order->id);
```

Di view order, tambahkan button untuk membayar:
```blade
@if ($order->status !== 'paid')
    <a href="{{ route('payment.create', $order->id) }}" class="btn btn-primary">
        Bayar Sekarang
    </a>
@endif
```

### B. Membuat Payment Snap
```php
$result = $midtransService->createSnapTransaction($orderId, $orderData);
// Returns: ['status' => 'success', 'snap_token' => '...']
```

### C. Handle Notification dari Midtrans
Webhook automatic di-handle di controller:
```php
POST /midtrans/notification
```

### D. Cek Status Payment
```php
GET /payment/status/{order}
```

## Credential Midtrans
Tambahkan credential di `.env`:
```
MIDTRANS_SERVER_KEY=Mid-server-xxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=Mid-client-xxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false
```

Dapatkan credential dari: https://dashboard.midtrans.com

## Installasi CSRF untuk Webhook
Webhook Midtrans harus bypass CSRF, sudah dikonfigurasi di middleware jika diperlukan.

## Testing Payment
1. Gunakan test credentials
2. Di halaman payment, klik "Bayar Sekarang"
3. Midtrans Snap akan terbuka
4. Gunakan test card dari Midtrans

## Production Setup
1. Ganti `MIDTRANS_IS_PRODUCTION=true` di `.env`
2. Ganti `MIDTRANS_SERVER_KEY` dan `MIDTRANS_CLIENT_KEY` dengan production keys
3. Setup HTTPS untuk security

## Troubleshooting

### 1. "Client key not set"
Pastikan `MIDTRANS_CLIENT_KEY` ada di `.env` dan `config/midtrans.php` benar.

### 2. Payment gagal/error
Check logs: `storage/logs/laravel.log`

### 3. Webhook tidak terima notifikasi
Pastikan route `/midtrans/notification` accessible dari internet (jika production).

### 4. Transaction tidak tersimpan
Check database untuk tabel `payments` sudah dibuat:
```bash
php artisan migrate
```

## Database Schema

### payments table
- id (PK)
- order_id (FK ke orders)
- snap_token
- transaction_id
- payment_type
- payment_status (pending/success/failed/cancelled)
- gross_amount
- fraud_status
- response_code
- status_message
- timestamps
