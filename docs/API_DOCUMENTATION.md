# API Documentation - Midtrans Payment Integration

## Base URL
```
http://localhost:8000
```

## Authentication
All endpoints except webhook require authentication (logged in user).

---

## Endpoints

### 1. Create Payment Snap Token
Create a payment snap token untuk order yang sudah ada.

**Endpoint:**
```
POST /payment/create/{order}
```

**Parameters:**
- `order` (URL parameter, required): Order ID

**Headers:**
```
Content-Type: application/json
X-CSRF-TOKEN: {csrf_token}
```

**Response (Success):**
```json
{
  "success": true,
  "snap_token": "0bb3c772-46de-4927-ae5f-b15e766410e8",
  "client_key": "Mid-client-xR2KgoNb83U_q9ac"
}
```

**Response (Error):**
```json
{
  "success": false,
  "message": "Order not found"
}
```

**Example:**
```bash
curl -X POST http://localhost:8000/payment/create/1 \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: YOUR_CSRF_TOKEN" \
  -H "Cookie: XSRF-TOKEN=YOUR_XSRF_TOKEN"
```

---

### 2. Get Payment Status
Mendapatkan status pembayaran untuk order tertentu.

**Endpoint:**
```
GET /payment/status/{order}
```

**Parameters:**
- `order` (URL parameter, required): Order ID

**Headers:**
```
Accept: application/json
```

**Response (Success):**
```json
{
  "success": true,
  "data": {
    "transaction_time": "2025-11-25 12:34:56",
    "transaction_status": "settlement",
    "transaction_id": "1234567890",
    "status_code": "200",
    "gross_amount": "105000.00",
    "currency": "IDR",
    "order_id": "INV-20251125-00001",
    "payment_type": "credit_card",
    "fraud_status": "accept"
  }
}
```

**Example:**
```bash
curl -X GET http://localhost:8000/payment/status/1 \
  -H "Accept: application/json"
```

---

### 3. Midtrans Webhook Notification
Endpoint untuk menerima notifikasi dari Midtrans. **Endpoint ini public dan tidak memerlukan CSRF token.**

**Endpoint:**
```
POST /midtrans/notification
```

**Content-Type:**
```
application/json
```

**Body (Example):**
```json
{
  "transaction_time": "2025-11-25 12:00:00",
  "transaction_status": "settlement",
  "transaction_id": "1234567890",
  "status_code": "200",
  "gross_amount": "105000.00",
  "order_id": "INV-20251125-00001",
  "payment_type": "credit_card",
  "currency": "IDR",
  "fraud_status": "accept",
  "signature_key": "SIGNATURE_KEY_FROM_MIDTRANS",
  "masked_card": "481111-1114",
  "bank": "bni",
  "eci": "05",
  "approval_code": "1234567890"
}
```

**Response:**
```json
{
  "status": "ok"
}
```

**Webhook Behavior:**
- `transaction_status: settlement` → Order status = "paid"
- `transaction_status: capture` → Order status = "paid" (credit card success)
- `transaction_status: pending` → Order status = "pending"
- `transaction_status: deny` → Order status = "failed"
- `transaction_status: cancel/expire` → Order status = "cancelled"
- `transaction_status: refund` → Order status = "refunded"

---

### 4. Payment Finish Callback
Redirect callback setelah pembayaran selesai.

**Endpoint:**
```
POST /payment/finish
```

**Query Parameters:**
- `order_id`: Order ID
- `status_code`: Status code dari Midtrans
- `transaction_status`: Transaction status

**Redirect to:**
```
/orders/{order_id}
```

---

### 5. Payment Error Callback
Redirect callback jika pembayaran error.

**Endpoint:**
```
POST /payment/error
```

**Query Parameters:**
- `order_id`: Order ID

**Redirect to:**
```
/orders/{order_id}?error=Payment failed
```

---

### 6. Payment Pending Callback
Redirect callback untuk pembayaran yang pending.

**Endpoint:**
```
POST /payment/pending
```

**Query Parameters:**
- `order_id`: Order ID
- `transaction_status`: Transaction status

**Redirect to:**
```
/orders/{order_id}?message=Payment pending
```

---

## Payment Flow Diagram

```
1. User completes checkout
   ↓
2. POST /payment/create/{order}
   ↓
3. Get snap_token
   ↓
4. Load Midtrans Snap (JavaScript)
   ↓
5. User fills payment details
   ↓
6. Midtrans processes payment
   ↓
7. Midtrans sends webhook
   POST /midtrans/notification
   ↓
8. Payment record updated in database
   Order status updated
   ↓
9. User redirected to callback URL
   /payment/finish or /payment/error
```

---

## Data Models

### Payment Model
```
{
  "id": 1,
  "order_id": 1,
  "snap_token": "0bb3c772-46de-4927-ae5f-b15e766410e8",
  "transaction_id": "1234567890",
  "payment_type": "credit_card",
  "payment_status": "settlement",
  "gross_amount": "105000.00",
  "fraud_status": "accept",
  "response_code": "200",
  "status_message": "Payment settlement success",
  "created_at": "2025-11-25T12:00:00Z",
  "updated_at": "2025-11-25T12:05:00Z"
}
```

### Order Model (Payment-related fields)
```
{
  "id": 1,
  "invoice_no": "INV-20251125-00001",
  "user_id": 1,
  "total": "105000.00",
  "status": "paid",
  "paid_at": "2025-11-25T12:05:00Z",
  "payment_method": "midtrans",
  ...
}
```

---

## Error Handling

### Common HTTP Status Codes

| Code | Meaning |
|------|---------|
| 200 | OK - Request berhasil |
| 400 | Bad Request - Parameter tidak valid |
| 401 | Unauthorized - User belum login |
| 403 | Forbidden - User tidak memiliki akses |
| 404 | Not Found - Order tidak ditemukan |
| 500 | Server Error - Error di server |

### Error Response Format
```json
{
  "success": false,
  "message": "Error description"
}
```

---

## Integration Example (JavaScript)

```javascript
// 1. Create payment snap
fetch('/payment/create/1', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
  }
})
.then(response => response.json())
.then(data => {
  if (data.success) {
    // 2. Open Midtrans Snap
    snap.pay(data.snap_token, {
      onSuccess: function(result) {
        console.log('Payment success');
        // User will be redirected to /payment/finish
      },
      onPending: function(result) {
        console.log('Payment pending');
        // User will be redirected to /payment/pending
      },
      onError: function(result) {
        console.log('Payment error');
        // User will be redirected to /payment/error
      }
    });
  }
});
```

---

## Configuration

### Environment Variables
```dotenv
MIDTRANS_SERVER_KEY=Mid-server-xxxxx
MIDTRANS_CLIENT_KEY=Mid-client-xxxxx
MIDTRANS_IS_PRODUCTION=false
```

### Config File: config/midtrans.php
```php
[
  'enabled' => env('MIDTRANS_ENABLED', true),
  'server_key' => env('MIDTRANS_SERVER_KEY'),
  'client_key' => env('MIDTRANS_CLIENT_KEY'),
  'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
  'app_name' => env('APP_NAME', 'Web Yaka'),
]
```

---

## Security Considerations

1. **CSRF Protection**: Webhook endpoint dikecualikan dari CSRF
2. **Authentication**: Semua user endpoints memerlukan login
3. **Authorization**: User hanya bisa akses order mereka sendiri
4. **Signature Verification**: Midtrans sign semua webhook (optional)
5. **HTTPS**: Gunakan HTTPS di production

---

## Support

Untuk bantuan lebih lanjut:
- Midtrans Docs: https://docs.midtrans.com
- Dashboard: https://dashboard.midtrans.com
- Status: https://status.midtrans.com
