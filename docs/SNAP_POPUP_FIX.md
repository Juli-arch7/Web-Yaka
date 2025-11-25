# 🔧 Solusi: Snap Popup Tidak Muncul - Step-by-Step Debugging

Server sudah running. Mari kita debug step-by-step.

## 🚨 Paling Mungkin Penyebab & Solusi Cepat

### Masalah 1: MIDTRANS_CLIENT_KEY Tidak Terbaca

**Cek di Browser:**
1. Buka halaman order: http://localhost:8000/orders/1
2. Tekan F12 (DevTools)
3. Klik tab **Console**
4. Ketik: `snap`
5. Jika error "snap is not defined", masalahnya adalah Snap JS tidak ter-load

**Solusi:**

Verifikasi `.env` file:
```bash
# Buka .env dan pastikan ada:
MIDTRANS_CLIENT_KEY=Mid-client-xR2KgoNb83U_q9ac
MIDTRANS_SERVER_KEY=Mid-server-msnMxVzFWKbhIoTxwfNiC2xL
MIDTRANS_IS_PRODUCTION=false
```

Clear cache:
```bash
php artisan config:clear
php artisan cache:clear
```

**Restart server:**
- Tekan `Ctrl+C` di terminal
- Jalankan: `php artisan serve`

---

### Masalah 2: Button Tidak Ada di Halaman

**Cek:**
1. Buka halaman order
2. Tekan Ctrl+F
3. Cari: "Bayar Sekarang"
4. Jika tidak ada, tombol tidak ter-render

**Solusi:**

Pastikan order status adalah "pending". Cek di halaman order, apakah ada status badge "Menunggu Pembayaran"?

Jika tidak:
```bash
# Di terminal, buka tinker:
php artisan tinker

# Cek order:
>>> $order = Order::find(1);
>>> $order->status
// Harus output: "pending"

# Jika bukan pending, ubah:
>>> $order->update(['status' => 'pending']);
>>> exit
```

---

### Masalah 3: Button Ada   tapi Tidak Bisa Diklik

**Cek Console untuk Debug Log:**

Kode sudah ada console.log yang detail. Buka DevTools Console dan klik tombol.

Anda seharusnya lihat:
```
[Payment] Snap available: true
[Payment] Order ID: 1
[Payment] Client Key: Mid-client-xR2KgoNb83U_q9ac
[Payment] Window loaded, snap status: object
[Payment] Button clicked
[Payment] Fetching URL: http://localhost:8000/payment/create/1
```

**Jika tidak ada log:**
- Tekan F5 refresh halaman
- Hard refresh: Ctrl+Shift+R
- Cek apakah ada JavaScript error di console

---

### Masalah 4: Fetch Request Fail (404, 500, dll)

**Di Console, lihat:**
```
[Payment] Response status: 404
```

**Jika 404:**
```bash
# Check routes:
php artisan route:list | grep payment
```

Pastikan ada route: `POST /payment/create/{order}`

**Jika 500:**

Lihat di terminal (di mana server running). Ada error message.

Atau buka logs:
```bash
# Di terminal lain:
tail -f storage/logs/laravel.log
```

---

### Masalah 5: Snap Object Undefined

**Console menunjukkan:**
```
[Payment] Snap object: undefined, undefined
```

**Solusi:**

Snap JS dari CDN belum ter-load. Bisa karena:
1. Network error → Cek internet
2. CDN blocked → Coba reload
3. CORS issue → Advanced

**Test manual:**
Di browser console, ketik:
```javascript
fetch('https://app.sandbox.midtrans.com/snap/snap.js')
  .then(r => r.text())
  .then(t => console.log('Loaded:', t.length, 'bytes'))
```

Jika error → CDN tidak bisa di-reach.

---

## 📋 Complete Debugging Checklist

Copy-paste ini ke browser console:

```javascript
console.log("=== MIDTRANS DEBUG ===");
console.log("1. Snap object:", typeof snap, snap);
console.log("2. Button element:", document.getElementById('pay-button'));
console.log("3. CSRF token:", document.querySelector('meta[name="csrf-token"]')?.content.substring(0,20));
console.log("4. Order ID:", '{{ $order->id }}' || 'NOT SET');
console.log("5. Client key from script:", '{{ config('midtrans.client_key') }}'.substring(0,20));
console.log("=== END DEBUG ===");
```

Share output di sini jika masih error.

---

## 🔄 Quick Fix Cycle

1. **Edit file:**
   ```bash
   # Update order view dengan code baru yang sudah di-commit
   # File: resources/views/orders/show.blade.php
   ```

2. **Clear cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```

3. **Test:**
   - Hard refresh browser: Ctrl+Shift+R
   - Open DevTools: F12
   - Click button
   - Check console logs

4. **Debug:**
   - Lihat console output
   - Share error messages

---

## 📝 Testing Order

Jika belum ada order, buat:

```bash
php artisan tinker

# Buat user:
$user = \App\Models\User::create([
    'name' => 'Test User',
    'email' => 'test@test.com',
    'password' => bcrypt('password'),
]);

# Buat order:
$order = \App\Models\Order::create([
    'user_id' => $user->id,
    'invoice_no' => 'INV-TEST-001',
    'shipping_address' => 'Test Address',
    'shipping_method' => 'Express',
    'shipping_cost' => 50000,
    'payment_method' => 'midtrans',
    'subtotal' => 100000,
    'discount' => 0,
    'total' => 150000,
    'status' => 'pending'
]);

echo "Order created: ID " . $order->id;
exit;
```

---

## 🎯 Expected Behavior

Jika semuanya benar:

1. **Halaman Order:**
   - Tombol "Bayar Sekarang" visible
   - Status badge "Menunggu Pembayaran" visible

2. **Klik Tombol:**
   - Console menunjukkan: `[Payment] Button clicked`
   - Tombol text berubah ke "Memproses..."

3. **After Fetch:**
   - Console: `[Payment] Got snap token, opening payment`
   - Popup Midtrans muncul

4. **In Popup:**
   - Bisa pilih metode pembayaran
   - Bisa input card details

5. **After Payment:**
   - Popup close
   - Halaman reload
   - Order status berubah ke "paid"

---

## 🚀 Quick Action Plan

Sekarang, ikuti ini:

### Step 1: Verify Files (2 min)
```bash
# File exists?
ls resources/views/orders/show.blade.php
ls app/Http/Controllers/PaymentController.php
```

### Step 2: Check Environment (1 min)
```bash
# Client key exists?
grep MIDTRANS_CLIENT_KEY .env
```

### Step 3: Clear Cache (1 min)
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Step 4: Test in Browser (5 min)
- Buka: http://localhost:8000/orders/1
- Press F12
- Click "Bayar Sekarang"
- Check Console tab

### Step 5: Report Issue (jika ada)
- Copy console log output
- Copy error message dari laravel.log
- Share dengan detail

---

## 📞 Common Error Messages & Fixes

| Error | Cause | Fix |
|-------|-------|-----|
| `snap is not defined` | Snap JS not loaded | Check CDN network |
| `snap.pay is not a function` | Snap JS loaded incorrectly | Clear browser cache |
| `404 Not Found` | Route doesn't exist | Run `php artisan route:list` |
| `CSRF token mismatch` | Missing token | Check `csrf_token()` in view |
| `Client key not set` | Config not loaded | Run `php artisan config:clear` |
| `Order not found` | Wrong Order ID | Verify $order->id in route |

---

**Good luck! 🚀**

Kalau masih error, share:
1. Browser console log (screenshot)
2. Terminal/server log (last 10 lines)
3. Step mana yang error
