# Quick Start Guide - Integrasi Midtrans

## 🚀 5 Langkah Implementasi Midtrans

### Langkah 1: Setup Environment
Edit `.env` dan pastikan ada:
```dotenv
MIDTRANS_SERVER_KEY=Mid-server-xxxxx
MIDTRANS_CLIENT_KEY=Mid-client-xxxxx
MIDTRANS_IS_PRODUCTION=false
```

### Langkah 2: Jalankan Migration
```bash
php artisan migrate
```
Tabel `payments` akan otomatis terbuat.

### Langkah 3: Tambahkan Payment Button di View Order
Di `resources/views/orders/show.blade.php`, tambahkan:

```blade
@if ($order->status === 'pending')
    <button id="pay-button" class="btn btn-primary">
        Bayar Sekarang
    </button>
@endif

@push('scripts')
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
                    snap.pay(data.snap_token);
                }
            });
        };
    </script>
@endpush
```

### Langkah 4: Test Payment
1. Jalankan server: `php artisan serve`
2. Login dan buat order
3. Klik "Bayar Sekarang"
4. Gunakan test card: `4811 1111 1111 1114`
5. CVV: `123`, Exp: `12/25`

### Langkah 5: Monitor Status
Setelah pembayaran:
- Cek database `payments` table
- Order status otomatis update ke "paid"
- User akan melihat notifikasi sukses

---

## ✅ Checklist Integrasi

- [x] Package `midtrans/midtrans-php` terinstall
- [x] Config file `config/midtrans.php` dibuat
- [x] Service `MidtransService.php` tersedia
- [x] Controller `PaymentController.php` siap
- [x] Model `Payment.php` dibuat
- [x] Migration untuk payments table dibuat
- [x] Routes payment sudah ditambahkan
- [x] Service provider terdaftar
- [x] CSRF exception untuk webhook dikonfigurasi
- [ ] Update payment button di view order (Anda harus lakukan)
- [ ] Test payment di development
- [ ] Update credentials untuk production
- [ ] Deploy ke production

---

## 📁 File yang Sudah Dibuat

```
✅ app/Services/MidtransService.php
✅ app/Http/Controllers/PaymentController.php
✅ app/Models/Payment.php
✅ config/midtrans.php
✅ database/migrations/2025_11_25_000000_create_payments_table.php
✅ resources/views/orders/payment.blade.php
✅ MIDTRANS_SETUP.md
✅ TESTING_PAYMENT.md
✅ API_DOCUMENTATION.md
✅ PAYMENT_VIEW_SNIPPET.md
```

---

## 🔧 Konfigurasi yang Sudah Dilakukan

✅ `bootstrap/app.php` - CSRF exception untuk webhook
✅ `routes/web.php` - Payment routes dan webhook
✅ `app/Providers/AppServiceProvider.php` - Service registration
✅ `composer.json` - Midtrans package terinstall

---

## 💻 Struktur API

### Create Payment
```
POST /payment/create/1
→ Return snap_token
→ Frontend load Midtrans Snap
```

### Webhook Notification
```
POST /midtrans/notification
← From Midtrans server
→ Update payment status
→ Update order status
```

### Payment Callbacks
```
/payment/finish  → Success
/payment/error   → Failed
/payment/pending → Pending
```

---

## 🧪 Testing Dengan Sandbox

**Midtrans Sandbox URLs:**
- Dashboard: https://dashboard.sandbox.midtrans.com
- Snap JS: https://app.sandbox.midtrans.com/snap/snap.js

**Test Cards:**
- Success: `4811 1111 1111 1114`
- Expired: `4911 1111 1111 1113`
- Challenge: `4111 1111 1111 1111`

---

## 📱 Frontend Integration

### Minimal Implementation
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
    .then(d => snap.pay(d.snap_token))
};
</script>
```

---

## 🔐 Security Tips

1. **Production Mode**
   ```
   MIDTRANS_IS_PRODUCTION=true
   ```

2. **HTTPS**
   Webhook harus via HTTPS di production

3. **Verify Signature**
   Bisa tambahkan signature verification untuk webhook

4. **Rate Limiting**
   Pertimbangkan rate limiting untuk payment endpoint

---

## 📚 Next Steps

1. **Customize Payment View**
   - Update UI sesuai brand
   - Tambahkan form validation
   - Improve UX

2. **Add Email Notifications**
   - Kirim email setelah payment sukses
   - Kirim invoice PDF

3. **Add Analytics**
   - Track payment success rate
   - Monitor failed transactions

4. **Production Deployment**
   - Setup SSL certificate
   - Configure production credentials
   - Test dengan real payment methods

5. **Add Refund Functionality**
   - Implementasi refund di admin panel
   - Audit trail untuk refund

---

## 🆘 Troubleshooting Cepat

| Problem | Solution |
|---------|----------|
| Payment button tidak muncul | Check MIDTRANS_CLIENT_KEY di .env |
| Snap popup tidak muncul | Verify snap.js loading |
| Webhook tidak diterima | Check route di routes/web.php |
| Order status tidak update | Run `php artisan migrate` |
| CSRF error di webhook | Check CSRF exception di bootstrap/app.php |

---

## 📞 Support Resources

- **Midtrans Documentation**: https://docs.midtrans.com
- **Midtrans Dashboard**: https://dashboard.midtrans.com
- **This Project Docs**:
  - [MIDTRANS_SETUP.md](./MIDTRANS_SETUP.md)
  - [TESTING_PAYMENT.md](./TESTING_PAYMENT.md)
  - [API_DOCUMENTATION.md](./API_DOCUMENTATION.md)

---

**Ready to accept payments! 🎉**
