# Testing Guide - Midtrans Integration

## Testing Payment Flow

### 1. Setup Test Environment
```bash
cd d:\code\Web-Yaka
php artisan serve
```

### 2. Test Credentials (Already in .env)
```
MIDTRANS_SERVER_KEY=Mid-server-msnMxVzFWKbhIoTxwfNiC2xL
MIDTRANS_CLIENT_KEY=Mid-client-xR2KgoNb83U_q9ac
MIDTRANS_IS_PRODUCTION=false
```

### 3. Test Payment Flow
1. Login ke aplikasi
2. Tambahkan produk ke cart
3. Lakukan checkout
4. Pada halaman order, klik "Bayar Sekarang"
5. Midtrans Snap popup akan muncul

### 4. Test Card Numbers
Gunakan card numbers berikut di Midtrans Sandbox:

#### Success Test
- Card Number: 4811 1111 1111 1114
- CVV: 123
- Valid Thru: 12/25

#### Expired Card Test
- Card Number: 4911 1111 1111 1113
- CVV: 123
- Valid Thru: 12/20

#### Deny Test
- Card Number: 5105 1051 0510 5100
- CVV: 123
- Valid Thru: 12/25

#### Challenge (3D Secure)
- Card Number: 4111 1111 1111 1111
- CVV: 123
- Valid Thru: 12/25
- OTP: 112233

### 5. Test Scenarios

#### A. Successful Payment
1. Use success test card
2. Fill in payment details
3. Complete payment
4. Should see "Pembayaran Berhasil"
5. Check database: Order status should be "paid"

#### B. Failed Payment
1. Use expired/deny card
2. Payment should fail
3. Should remain on payment page
4. Order status should remain "pending"

#### C. Pending Payment
1. Use payment method that requires verification
2. Payment should be pending
3. Webhook will update status when confirmed

#### D. Cancelled Payment
1. Start payment
2. Close Midtrans popup without completing
3. Order status should remain "pending"

### 6. Check Database

#### View Payment Records
```bash
php artisan tinker
>>> Payment::all()
>>> Payment::with('order')->get()
```

#### View Order Status
```bash
>>> Order::with('items')->latest()->first()
```

### 7. Webhook Testing

#### Check Webhook Logs
Semua webhook dari Midtrans di-log di database `payments` table.

```bash
>>> Payment::latest()->first()
// Check: payment_status, transaction_id, fraud_status, etc
```

#### Manual Webhook Test
```bash
POST http://localhost:8000/midtrans/notification
Content-Type: application/json

{
  "transaction_time": "2025-11-25 12:00:00",
  "gross_amount": "100000.00",
  "order_id": "INV-20251125-00001",
  "payment_type": "credit_card",
  "signature_key": "xxx",
  "status_code": "200",
  "transaction_status": "settlement",
  "transaction_id": "xxx",
  "fraud_status": "accept"
}
```

### 8. API Endpoints to Test

#### Create Payment
```bash
POST /payment/create/1
```

#### Get Payment Status
```bash
GET /payment/status/1
```

#### Midtrans Notification (Webhook)
```bash
POST /midtrans/notification
```

#### Payment Callbacks
```bash
POST /payment/finish
POST /payment/error
POST /payment/pending
```

### 9. Debugging

#### Enable Debug Mode
Set di `.env`:
```
APP_DEBUG=true
```

#### Check Logs
```bash
tail -f storage/logs/laravel.log
```

#### Database Queries
```bash
DB::enableQueryLog();
// ... perform actions ...
dd(DB::getQueryLog());
```

### 10. Common Issues & Solutions

#### Issue: "Client Key not found"
- Solution: Check `config/midtrans.php` dan `.env`

#### Issue: Payment button not loading
- Solution: Check browser console for JS errors
- Verify Snap.js loading from CDN

#### Issue: Webhook not received
- Solution: Check if route is accessible
- Verify CSRF exception if needed

#### Issue: Transaction not saved to database
- Solution: Check `payments` table exists
- Run: `php artisan migrate`

### 11. Test Checklist

- [ ] Payment snap loads correctly
- [ ] Can input test card
- [ ] Payment success updates order status
- [ ] Webhook updates database
- [ ] Payment history visible in account
- [ ] Failed payment keeps order pending
- [ ] Cancelled payment keeps order pending
- [ ] Card validation working
- [ ] Error messages clear
- [ ] UI responsive

### 12. Production Testing

Before going live:
1. Update credentials to production keys
2. Set `MIDTRANS_IS_PRODUCTION=true`
3. Test with real cards (small amounts)
4. Verify webhook endpoint is publicly accessible
5. Test with different payment methods
6. Verify email notifications work
7. Check settlement process
