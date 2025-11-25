# Debug Payment Script untuk Windows
# Jalankan di PowerShell

Write-Host "╔════════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║          Midtrans Payment - Debug & Verification             ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan

# 1. Check Environment
Write-Host "`n[1] Checking Environment Variables..." -ForegroundColor Yellow
php artisan config:show midtrans

# 2. Check Routes
Write-Host "`n[2] Checking Payment Routes..." -ForegroundColor Yellow
php artisan route:list | Select-String "payment"

# 3. Check Database & Service
Write-Host "`n[3] Checking Components..." -ForegroundColor Yellow

$checks = @(
    @{ Name = "Payments Table"; File = "database/migrations/2025_11_25_000000_create_payments_table.php" },
    @{ Name = "PaymentController"; File = "app/Http/Controllers/PaymentController.php" },
    @{ Name = "MidtransService"; File = "app/Services/MidtransService.php" },
    @{ Name = "Payment Model"; File = "app/Models/Payment.php" },
    @{ Name = "Config File"; File = "config/midtrans.php" }
)

foreach ($check in $checks) {
    if (Test-Path $check.File) {
        Write-Host "  ✓ $($check.Name)" -ForegroundColor Green
    } else {
        Write-Host "  ✗ $($check.Name) - FILE NOT FOUND!" -ForegroundColor Red
    }
}

# 4. Check if Server Key is set
Write-Host "`n[4] Checking Credentials..." -ForegroundColor Yellow
$serverKey = (php -r "echo getenv('MIDTRANS_SERVER_KEY');")
$clientKey = (php -r "echo getenv('MIDTRANS_CLIENT_KEY');")

if ($serverKey) {
    Write-Host "  ✓ Server Key: SET (${serverKey:0:20}...)" -ForegroundColor Green
} else {
    Write-Host "  ✗ Server Key: NOT SET!" -ForegroundColor Red
}

if ($clientKey) {
    Write-Host "  ✓ Client Key: SET (${clientKey:0:20}...)" -ForegroundColor Green
} else {
    Write-Host "  ✗ Client Key: NOT SET!" -ForegroundColor Red
}

# 5. Clear Cache
Write-Host "`n[5] Clearing Cache..." -ForegroundColor Yellow
php artisan config:clear 2>&1 | Out-Null
php artisan cache:clear 2>&1 | Out-Null
php artisan view:clear 2>&1 | Out-Null
Write-Host "  ✓ Cache cleared" -ForegroundColor Green

# 6. Test Payment Endpoint (if test order exists)
Write-Host "`n[6] Testing Endpoint..." -ForegroundColor Yellow
$testOrderId = php -r "echo \App\Models\Order::first()?->id ?? 0;"
if ($testOrderId -and $testOrderId -ne "0") {
    Write-Host "  ✓ Test order found (ID: $testOrderId)" -ForegroundColor Green
    Write-Host "  You can test payment with: POST /payment/create/$testOrderId" -ForegroundColor Cyan
} else {
    Write-Host "  ⚠ No test order found. Create an order first." -ForegroundColor Yellow
}

# 7. Summary
Write-Host "`n╔════════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║                    Next Steps:                                 ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host "
1. Start server:
   php artisan serve

2. Open browser and navigate to an order:
   http://localhost:8000/orders/1

3. Click 'Bayar Sekarang' button

4. Debug Issues:
   - Open Browser DevTools (F12)
   - Go to Console tab
   - You should see debug logs starting with '[Payment]'
   - Check for any errors

5. If Snap popup doesn't show:
   - Check browser console for errors
   - Verify MIDTRANS_CLIENT_KEY is set in .env
   - Run: php artisan config:clear
   - Hard refresh browser: Ctrl+Shift+R

6. Check logs:
   tail -f storage/logs/laravel.log
" -ForegroundColor Cyan
