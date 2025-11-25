# 🚀 Midtrans Integration Cheat Sheet

Quick reference untuk developer yang ingin menggunakan payment di Web Yaka.

---

## 🔥 Quick Commands

```bash
# Setup
composer install
php artisan migrate
php artisan serve

# Testing
php artisan tinker

# Debug
php artisan config:show midtrans
tail -f storage/logs/laravel.log
```

---

## 💻 Code Snippets

### 1. Create Payment (Backend)
```php
use App\Services\MidtransService;

$midtrans = new MidtransService();
$result = $midtrans->createSnapTransaction(
    $order->invoice_no,
    [
        'total' => $order->total,
        'customer_name' => $user->name,
        'customer_email' => $user->email,
        'items' => [...],
    ]
);

return response()->json([
    'snap_token' => $result['snap_token']
]);
```

### 2. Create Payment (Frontend)
```html
<button id="pay-btn">Bayar Sekarang</button>

<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="YOUR_CLIENT_KEY"></script>

<script>
document.getElementById('pay-btn').onclick = function() {
    fetch('/payment/create/{{ $order->id }}', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': token}
    })
    .then(r => r.json())
    .then(d => {
        snap.pay(d.snap_token, {
            onSuccess: () => location.reload(),
            onError: () => alert('Error')
        });
    });
};
</script>
```

### 3. Get Payment Status
```php
$status = Payment::with('order')
    ->where('order_id', $orderId)
    ->latest()
    ->first();

echo $status->payment_status; // pending, settlement, etc
```

### 4. Handle Notification (Middleware)
```php
// Auto-handled di PaymentController::notification()
// Webhook dari Midtrans akan auto-update:
// - Payment record
// - Order status
// - Database transaction
```

---

## 🗂️ File Locations

```
Service      : app/Services/MidtransService.php
Controller   : app/Http/Controllers/PaymentController.php
Model        : app/Models/Payment.php
Config       : config/midtrans.php
Migration    : database/migrations/2025_11_25_000000_create_payments_table.php
View         : resources/views/orders/payment.blade.php
Routes       : routes/web.php (search: "Payment")
```

---

## 📡 API Endpoints

```bash
# Create payment snap
POST /payment/create/1

# Get payment status
GET /payment/status/1

# Webhook (auto-handled)
POST /midtrans/notification

# Callbacks (user redirected)
POST /payment/finish
POST /payment/error
POST /payment/pending
```

---

## 🔑 Environment Setup

```dotenv
MIDTRANS_SERVER_KEY=Mid-server-xxxxx
MIDTRANS_CLIENT_KEY=Mid-client-xxxxx
MIDTRANS_IS_PRODUCTION=false  # Change to true for production
```

---

## 🧪 Test Cards

| Card Type | Number | CVV | Exp |
|-----------|--------|-----|-----|
| Success | 4811 1111 1111 1114 | 123 | 12/25 |
| Challenge | 4111 1111 1111 1111 | 123 | 12/25 |
| Deny | 5105 1051 0510 5100 | 123 | 12/25 |

---

## 🔗 Payment Status Flow

```
pending (Start)
    ↓
capture / settlement (Success)
    ↓
paid (Order updated)

---

pending (Start)
    ↓
deny / cancel / expire (Failed)
    ↓
failed / cancelled (Order remains pending)

---

pending (Start)
    ↓
pending (Awaiting confirmation)
    ↓
(manual review needed)
```

---

## 📊 Database Queries

```php
// Get all payments
Payment::all();

// Get payment for order
Payment::where('order_id', 1)->first();

// Get successful payments
Payment::where('payment_status', 'settlement')->get();

// Get failed payments
Payment::whereIn('payment_status', ['deny', 'cancel'])->get();

// Get with order info
Payment::with('order.user')->get();
```

---

## 🛡️ Security Checklist

- [ ] CSRF exception configured for webhook
- [ ] HTTPS enabled (production)
- [ ] Server key kept secret
- [ ] Input validation on all endpoints
- [ ] Authentication on payment endpoints
- [ ] Database transactions for atomicity
- [ ] Error messages don't leak sensitive data

---

## ⚙️ Configuration

### Development
```php
'is_production' => false,
'isSanitized' => true,
'is3ds' => true,
```

### Production
```php
'is_production' => true,
// Use production keys
'server_key' => env('MIDTRANS_SERVER_KEY'),
'client_key' => env('MIDTRANS_CLIENT_KEY'),
```

---

## 🐛 Debugging

```php
// Enable debug mode
APP_DEBUG=true

// Check payment record
>>> Payment::find(1)

// Check order status
>>> Order::with('payment')->find(1)

// Get latest payment
>>> Payment::latest()->first()

// Get payment details
>>> Payment::with('order.items.product')->find(1)
```

---

## 📦 Package Info

```
Name: midtrans/midtrans-php
Version: ^2.6
Namespace: Midtrans\*
Main Classes:
  - Config (Configuration)
  - Snap (Snap API)
  - Transaction (Transaction API)
```

---

## 🔄 Complete Payment Flow

```
1. User → Click "Bayar Sekarang"

2. Frontend → fetch('/payment/create/{order}', POST)

3. PaymentController::createPayment()
   → MidtransService::createSnapTransaction()
   → Snap::getSnapToken() [Call Midtrans API]
   → Save Payment record
   → Return snap_token

4. Frontend → snap.pay(snap_token)
   → Midtrans Snap opens
   → User fills payment form

5. Midtrans → Process payment
   → Success/Failed/Pending

6. Midtrans → webhook POST /midtrans/notification
   → PaymentController::notification()
   → MidtransService::handleNotification()
   → Update Payment record
   → Update Order status

7. Midtrans → Redirect to callback
   → /payment/finish
   → /payment/error
   → /payment/pending

8. Frontend → User sees status
```

---

## 🎯 Common Tasks

### Check if payment received
```php
$payment = Payment::where('order_id', $id)->first();
if ($payment->payment_status === 'settlement') {
    // Payment confirmed
}
```

### Mark order as paid
```php
// Auto done by webhook, but can manual:
$order->update(['status' => 'paid', 'paid_at' => now()]);
```

### Cancel payment
```php
$midtrans = new MidtransService();
$result = $midtrans->cancelTransaction($order->invoice_no);
```

### Refund payment
```php
$midtrans = new MidtransService();
$result = $midtrans->refundTransaction($order->invoice_no, $amount);
```

---

## 📈 Monitoring

### Key Metrics to Track
- Total transactions
- Success rate
- Failed transactions
- Pending transactions
- Payment methods distribution
- Average transaction amount

### SQL Queries for Monitoring
```sql
-- Total payments
SELECT COUNT(*) FROM payments;

-- By status
SELECT payment_status, COUNT(*) FROM payments 
GROUP BY payment_status;

-- Total revenue
SELECT SUM(gross_amount) FROM payments 
WHERE payment_status = 'settlement';
```

---

## 🚀 Deployment Checklist

- [ ] Update to production Midtrans keys
- [ ] Set MIDTRANS_IS_PRODUCTION=true
- [ ] Enable HTTPS
- [ ] Configure webhook IP whitelist (if needed)
- [ ] Set up email notifications
- [ ] Enable monitoring & alerts
- [ ] Test with real card (small amount)
- [ ] Verify settlement process
- [ ] Set up backup & recovery
- [ ] Document deployment steps

---

## 📞 Resources

| Resource | URL |
|----------|-----|
| Midtrans Docs | https://docs.midtrans.com |
| Snap Docs | https://docs.midtrans.com/en/snap/overview |
| Dashboard | https://dashboard.midtrans.com |
| Sandbox | https://app.sandbox.midtrans.com |
| API Reference | https://api-docs.midtrans.com |

---

## 💡 Pro Tips

1. **Use Tinker for Quick Testing**
   ```bash
   php artisan tinker
   >>> Payment::latest()->first()
   ```

2. **Monitor Webhook**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Test Different Scenarios**
   - Success payment
   - Failed payment
   - Pending payment
   - Cancelled payment

4. **Check Signature Key**
   - Midtrans signs all webhooks
   - Can verify for extra security

5. **Use .env for Credentials**
   - Never hardcode keys
   - Use config() helper

---

## ❌ Common Mistakes to Avoid

- ❌ Hardcoding API keys
- ❌ Skipping CSRF exception for webhook
- ❌ Not verifying payment status
- ❌ Missing error handling
- ❌ Not logging transactions
- ❌ Testing with production mode
- ❌ Not handling webhook timeout
- ❌ Trusting only client-side payment

---

## ✅ You're Ready!

Everything is set up. Just:

1. Update payment button in view
2. Test with sandbox card
3. Deploy to production

**Happy payments! 💳🎉**
