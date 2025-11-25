#!/bin/bash
# Debug Script untuk Midtrans Payment

echo "╔════════════════════════════════════════════════════════════════╗"
echo "║          Midtrans Payment - Debug & Verification             ║"
echo "╚════════════════════════════════════════════════════════════════╝"

cd "$(dirname "$0")"

# 1. Check Environment
echo ""
echo "[1] Checking Environment Variables..."
php artisan config:show midtrans

# 2. Check Routes
echo ""
echo "[2] Checking Payment Routes..."
php artisan route:list | grep payment

# 3. Check Database
echo ""
echo "[3] Checking Database..."
php artisan tinker <<'EOT'
echo "Payments table status: ";
try {
    $count = DB::table('payments')->count();
    echo "✓ OK (Records: $count)\n";
} catch (Exception $e) {
    echo "✗ Error\n";
}

echo "Test Order: ";
$order = \App\Models\Order::first();
if ($order) {
    echo "✓ Found (ID: {$order->id}, Status: {$order->status})\n";
} else {
    echo "✗ No orders found\n";
}
exit;
EOT

# 4. Clear Cache
echo ""
echo "[4] Clearing Cache..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
echo "✓ Cache cleared"

# 5. Ready for Testing
echo ""
echo "╔════════════════════════════════════════════════════════════════╗"
echo "║                    Ready for Testing!                         ║"
echo "║                                                                ║"
echo "║  1. Start server: php artisan serve                           ║"
echo "║  2. Open browser: http://localhost:8000                       ║"
echo "║  3. Create order and test payment                             ║"
echo "║  4. Check browser console (F12) for debug logs                ║"
echo "╚════════════════════════════════════════════════════════════════╝"
