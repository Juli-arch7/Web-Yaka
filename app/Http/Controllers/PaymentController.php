<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Membuat payment snap
     */
    public function createPayment($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Prepare order data
        $orderData = [
            'total' => $order->total,
            'customer_name' => $order->user->name ?? 'Customer',
            'customer_email' => $order->user->email ?? 'customer@example.com',
            'customer_phone' => $order->user->phone ?? '',
            'shipping_address' => $order->shipping_address,
            'items' => $this->formatItems($order),
        ];

        $result = $this->midtransService->createSnapTransaction($order->invoice_no, $orderData);

        if ($result['status'] === 'success') {
            // Save payment record
            Payment::create([
                'order_id' => $order->id,
                'snap_token' => $result['snap_token'],
                'gross_amount' => $order->total,
                'payment_status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'snap_token' => $result['snap_token'],
                'client_key' => config('midtrans.client_key'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
        ], 400);
    }

    /**
     * Handle notification dari Midtrans
     */
    public function notification(Request $request)
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json);

        $response = $this->midtransService->handleNotification($data);

        $payment = Payment::where('transaction_id', $data->transaction_id ?? null)->first();

        if (!$payment) {
            // Cari berdasarkan order_id jika transaction_id belum tersimpan
            $orderId = str_replace('INV-', '', $response['order_id']);
            $payment = Payment::whereHas('order', function ($query) use ($orderId) {
                $query->where('invoice_no', $response['order_id']);
            })->first();
        }

        if ($payment) {
            $payment->update([
                'transaction_id' => $data->transaction_id ?? null,
                'payment_type' => $response['payment_type'] ?? null,
                'payment_status' => $response['order_status'] ?? 'pending',
                'fraud_status' => $data->fraud_status ?? null,
                'response_code' => $data->response_code ?? null,
                'status_message' => $response['message'] ?? null,
            ]);

            // Update order status
            if ($response['order_status'] === 'paid') {
                $payment->order->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Callback setelah finish pembayaran
     */
    public function finish(Request $request)
    {
        $orderId = $request->query('order_id');
        $statusCode = $request->query('status_code');
        $transactionStatus = $request->query('transaction_status');

        // Redirect ke halaman order dengan pesan
        return redirect()->route('orders.show', $orderId)->with('message', 'Payment processing');
    }

    /**
     * Callback jika ada error
     */
    public function error(Request $request)
    {
        $orderId = $request->query('order_id');

        return redirect()->route('orders.show', $orderId)->with('error', 'Payment failed');
    }

    /**
     * Callback jika payment pending
     */
    public function pending(Request $request)
    {
        $orderId = $request->query('order_id');

        return redirect()->route('orders.show', $orderId)->with('message', 'Payment pending');
    }

    /**
     * Format items untuk Midtrans
     */
    private function formatItems($order)
    {
        $items = [];

        foreach ($order->items as $item) {
            $items[] = [
                'id' => $item->product_id,
                'price' => (int) $item->price,
                'quantity' => $item->quantity,
                'name' => $item->product->name ?? 'Product',
            ];
        }

        // Add shipping cost
        if ($order->shipping_cost > 0) {
            $items[] = [
                'id' => 'shipping',
                'price' => (int) $order->shipping_cost,
                'quantity' => 1,
                'name' => $order->shipping_method ?? 'Shipping',
            ];
        }

        // Add discount
        if ($order->discount > 0) {
            $items[] = [
                'id' => 'discount',
                'price' => (int) (-$order->discount),
                'quantity' => 1,
                'name' => 'Discount',
            ];
        }

        return $items;
    }

    /**
     * Mendapatkan status pembayaran
     */
    public function status($orderId)
    {
        $order = Order::findOrFail($orderId);
        $result = $this->midtransService->getTransactionStatus($order->invoice_no);

        if ($result['status'] === 'success') {
            return response()->json([
                'success' => true,
                'data' => $result['data'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
        ], 400);
    }
}
