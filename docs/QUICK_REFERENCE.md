# 📌 QUICK REFERENCE CARD

## ⚡ One-Liner Commands

```bash
# Start development
php artisan serve

# Run migrations
php artisan migrate

# Clear cache
php artisan config:clear

# List routes with payment
php artisan route:list | grep payment

# Tinker shell
php artisan tinker

# Run tests
php artisan test
```

## 🔗 Key Routes

| Method | Route | Name |
|--------|-------|------|
| POST | `/payment/create/{order}` | `payment.create` |
| GET | `/payment/status/{order}` | `payment.status` |
| POST | `/midtrans/notification` | `midtrans.notification` |
| POST | `/payment/finish` | `payment.finish` |
| POST | `/payment/error` | `payment.error` |
| POST | `/payment/pending` | `payment.pending` |

## 💻 Service Methods

```php
// Create payment snap
$midtrans->createSnapTransaction($orderId, $data)

// Get status
$midtrans->getTransactionStatus($orderId)

// Handle webhook
$midtrans->handleNotification($body)

// Cancel
$midtrans->cancelTransaction($orderId)

// Refund
$midtrans->refundTransaction($orderId, $amount)
```

## 🗄️ Model Access

```php
// Get payment
$payment = Payment::find(1);

// With order
$payment = Payment::with('order')->find(1);

// Latest payment
$payment = Payment::latest()->first();

// By order
$payment = Order::find(1)->payment;
```

## 🎫 Database

```sql
-- All payments
SELECT * FROM payments;

-- By status
SELECT * FROM payments WHERE payment_status = 'settlement';

-- By order
SELECT * FROM payments WHERE order_id = 1;

-- Recent
SELECT * FROM payments ORDER BY created_at DESC LIMIT 10;
```

## 🧪 Test Cards

| Type | Card | CVV | Exp |
|------|------|-----|-----|
| Success | 4811111111111114 | 123 | 12/25 |
| Challenge | 4111111111111111 | 123 | 12/25 |
| Deny | 5105105105105100 | 123 | 12/25 |

## ⚙️ Config Files

- **Midtrans Config**: `config/midtrans.php`
- **Routes**: `routes/web.php`
- **Environment**: `.env`
- **App Bootstrap**: `bootstrap/app.php`
- **Service Provider**: `app/Providers/AppServiceProvider.php`

## 📂 Code Files

| File | Purpose |
|------|---------|
| `app/Services/MidtransService.php` | Service layer |
| `app/Http/Controllers/PaymentController.php` | HTTP handling |
| `app/Models/Payment.php` | Model |
| `config/midtrans.php` | Config |
| `database/migrations/...create_payments_table.php` | Migration |

## 🔐 .env Variables

```
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_ENABLED=true
```

## 📊 Payment Status Flow

```
pending → settlement (success) → paid
       → deny/cancel/expire → failed
       → pending → (manual review)
```

## 🚀 Frontend Implementation

```html
<button id="pay-btn">Bayar</button>

<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="YOUR_CLIENT_KEY"></script>

<script>
document.getElementById('pay-btn').onclick = function() {
    fetch('/payment/create/1', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': token}
    })
    .then(r => r.json())
    .then(d => snap.pay(d.snap_token));
};
</script>
```

## 📚 Documentation

| Doc | For |
|-----|-----|
| `QUICK_START.md` | Fast setup |
| `MIDTRANS_SETUP.md` | Detailed setup |
| `TESTING_PAYMENT.md` | Testing |
| `API_DOCUMENTATION.md` | API ref |
| `CHEAT_SHEET.md` | Code snippets |
| `IMPLEMENTATION_GUIDE.md` | Next steps |

## ✅ Checklist

- [x] Backend setup
- [x] Database migration
- [x] Routes configured
- [x] Service registered
- [ ] Frontend button added
- [ ] Testing completed
- [ ] Production ready

---

**Questions? Check IMPLEMENTATION_GUIDE.md** 📖
