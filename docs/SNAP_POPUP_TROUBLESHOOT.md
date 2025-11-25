# 🐛 Troubleshooting: Snap Popup Tidak Muncul

## Penyebab Umum & Solusi

### 1. ❌ Client Key Tidak Terbaca

**Gejala:**
- Error di console: "Client key not set"
- Snap popup tidak muncul

**Solusi:**

Buka halaman order di browser dan buka **Developer Tools** (F12):
- Klik tab **Console**
- Lihat ada error apa

Jika ada error, periksa `.env`:
```bash
MIDTRANS_CLIENT_KEY=Mid-client-xR2KgoNb83U_q9ac
```

Pastikan di view order, ada:
```blade
data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"
```

---

### 2. ❌ Snap JavaScript Tidak Ter-load

**Gejala:**
- `snap.pay()` undefined
- Error: "snap is not defined"

**Solusi:**

Di browser DevTools Console, ketik:
```javascript
console.log(snap)
```

Jika undefined, berarti Snap JS tidak ter-load. Periksa:

```blade
<!-- Pastikan ada di view -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
```

---

### 3. ❌ Fetch Request Error

**Gejala:**
- Tombol diklik tapi tidak ada respon
- Console error: "Failed to fetch"

**Solusi:**

Tambahkan `console.log` untuk debug di browser console:

```javascript
document.getElementById('pay-button').onclick = function() {
    console.log('Button clicked');
    
    fetch('{{ route("payment.create", $order->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => {
        console.log('Response:', response);
        return response.json();
    })
    .then(data => {
        console.log('Data:', data);
        if (data.success) {
            console.log('Snap token:', data.snap_token);
            snap.pay(data.snap_token);
        } else {
            console.error('Error:', data.message);
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
    });
};
```

---

### 4. ❌ CSRF Token Mismatch

**Gejala:**
- Error 419 Expired Token
- Halaman di-refresh

**Solusi:**

Pastikan CSRF token ada di view:
```blade
<meta name="csrf-token" content="{{ csrf_token() }}">
```

Atau di script:
```javascript
'X-CSRF-TOKEN': '{{ csrf_token() }}'
```

---

### 5. ❌ Order ID Tidak Valid

**Gejala:**
- Error 404 dari server
- Console: "Order not found"

**Solusi:**

Pastikan order ID benar di route:
```blade
<!-- Correct -->
{{ route("payment.create", $order->id) }}

<!-- Not: $order or $order->invoice_no -->
```

---

### 6. ❌ MidtransService Error

**Gejala:**
- Server return 400/500 error
- Message: "Error creating snap"

**Solusi:**

Cek logs:
```bash
tail -f storage/logs/laravel.log
```

Periksa:
1. Server Key di .env correct
2. Order data valid
3. Amount tidak 0

---

### 7. ❌ View Tidak Ter-update

**Gejala:**
- Tombol bayar tidak ada di halaman
- Perubahan code tidak terlihat

**Solusi:**

Clear cache:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

Refresh browser (Hard refresh):
```
Ctrl + Shift + R  (Windows)
Cmd + Shift + R   (Mac)
```

---

## 🔍 Debugging Step-by-Step

### Step 1: Cek Environment Variables

```bash
php artisan config:show midtrans
```

Expected output:
```
client_key: Mid-client-xR2KgoNb83U_q9ac
server_key: Mid-server-msnMxVzFWKbhIoTxwfNiC2xL
is_production: false
```

### Step 2: Cek Routes

```bash
php artisan route:list | grep payment
```

Expected: 5+ payment routes

### Step 3: Test Endpoint Langsung

Di browser, akses:
```
POST http://localhost:8000/payment/create/1
```

Dengan headers:
```
X-CSRF-TOKEN: <your-token>
Content-Type: application/json
```

Expected response:
```json
{
  "success": true,
  "snap_token": "0bb3c772-46de-4927-ae5f-b15e766410e8",
  "client_key": "Mid-client-xR2KgoNb83U_q9ac"
}
```

### Step 4: Cek Browser Console

1. Buka halaman order
2. Buka DevTools (F12)
3. Klik tab Console
4. Ketik: `snap`
5. Pastikan tidak error

### Step 5: Test Snap.pay()

Di console, ketik:
```javascript
snap.pay('test-token-here')
```

Jika popup muncul, berarti Snap JS bekerja.

---

## 📝 Checklist Testing

Sebelum claim "tidak bisa", pastikan:

- [ ] `.env` punya `MIDTRANS_CLIENT_KEY`
- [ ] View punya `data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"`
- [ ] Snap JS script tag ada
- [ ] Button onclick handler ada
- [ ] Fetch route benar: `{{ route("payment.create", $order->id) }}`
- [ ] CSRF token ada
- [ ] Browser console tidak ada error
- [ ] Order ID valid (ada di database)
- [ ] Cache di-clear

---

## 🎯 Complete Working Example

File: `resources/views/orders/show.blade.php`

```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Order #{{ $order->invoice_no }}</h1>
    
    <!-- Payment Status -->
    @if ($order->status === 'paid')
        <div class="alert alert-success">✓ Pembayaran diterima</div>
    @else
        <div class="alert alert-warning">⚠ Belum dibayar</div>
        <button id="pay-button" class="btn btn-primary">Bayar Sekarang</button>
    @endif
    
    <!-- Order Details -->
    <table class="table">
        <tr>
            <td>Total:</td>
            <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
        </tr>
    </table>
</div>

@push('scripts')
    <!-- PENTING: Snap JS harus di-load -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    
    @if ($order->status !== 'paid')
    <script>
        document.getElementById('pay-button').onclick = function() {
            // Disable button
            this.disabled = true;
            this.innerText = 'Memproses...';
            
            // Fetch snap token
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
                    // Open Snap popup
                    snap.pay(data.snap_token, {
                        onSuccess: function(result) {
                            alert('Payment successful!');
                            window.location.reload();
                        },
                        onPending: function(result) {
                            alert('Payment pending');
                            window.location.reload();
                        },
                        onError: function(result) {
                            alert('Payment failed!');
                            window.location.reload();
                        }
                    });
                } else {
                    alert('Error: ' + data.message);
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Request failed');
                location.reload();
            });
        };
    </script>
    @endif
@endpush

@endsection
```

---

## 🆘 Masih Error?

1. **Screenshot console error dan share**
2. **Cek logs**: `storage/logs/laravel.log`
3. **Verify .env**: MIDTRANS_CLIENT_KEY ada?
4. **Check routes**: `php artisan route:list | grep payment`
5. **Verify view**: Snap script ada?

---

## ✅ Jika Sudah Bekerja

Pastikan:
- [ ] Snap popup muncul saat klik button
- [ ] Payment form terbuka
- [ ] Bisa pilih payment method
- [ ] Setelah submit, order status update

Congrats! 🎉
