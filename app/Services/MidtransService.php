<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Exception;

class MidtransService
{
    public function __construct()
    {
        $this->setupMidtransConfig();
    }

    private function setupMidtransConfig()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Membuat transaksi Snap URL
     */
    public function createSnapTransaction($orderId, $orderData)
    {
        try {
            $this->setupMidtransConfig();

            $payload = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $orderData['total'],
                ],
                'customer_details' => [
                    'first_name' => $orderData['customer_name'] ?? '',
                    'email' => $orderData['customer_email'] ?? '',
                    'phone' => $orderData['customer_phone'] ?? '',
                    'billing_address' => [
                        'address' => $orderData['shipping_address'] ?? '',
                    ],
                    'shipping_address' => [
                        'address' => $orderData['shipping_address'] ?? '',
                    ],
                ],
                'item_details' => $orderData['items'] ?? [],
                'callbacks' => [
                    'finish' => route('payment.finish'),
                    'error' => route('payment.error'),
                    'pending' => route('payment.pending'),
                ],
            ];

            $snapToken = Snap::getSnapToken($payload);
            return [
                'status' => 'success',
                'snap_token' => $snapToken,
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Mendapatkan status transaksi
     */
    public function getTransactionStatus($orderId)
    {
        try {
            $this->setupMidtransConfig();
            $status = Transaction::status($orderId);
            return [
                'status' => 'success',
                'data' => $status,
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle Midtrans notification
     */
    public function handleNotification($notificationBody)
    {
        try {
            $this->setupMidtransConfig();
            $notification = Transaction::notification($notificationBody);

            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $paymentType = $notification->payment_type;

            $response = [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'payment_type' => $paymentType,
            ];

            if ($transactionStatus == 'capture') {
                if ($paymentType == 'credit_card') {
                    if ($notification->fraud_status == 'challenge') {
                        $response['message'] = 'Payment fraud challenge';
                    } else {
                        $response['message'] = 'Payment success';
                        $response['order_status'] = 'paid';
                    }
                }
            } elseif ($transactionStatus == 'settlement') {
                $response['message'] = 'Payment settlement success';
                $response['order_status'] = 'paid';
            } elseif ($transactionStatus == 'pending') {
                $response['message'] = 'Payment pending';
                $response['order_status'] = 'pending';
            } elseif ($transactionStatus == 'deny') {
                $response['message'] = 'Payment denied';
                $response['order_status'] = 'failed';
            } elseif ($transactionStatus == 'cancel' || $transactionStatus == 'expire') {
                $response['message'] = 'Payment cancelled/expired';
                $response['order_status'] = 'cancelled';
            } elseif ($transactionStatus == 'refund') {
                $response['message'] = 'Payment refunded';
                $response['order_status'] = 'refunded';
            }

            return $response;
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Cancel transaksi
     */
    public function cancelTransaction($orderId)
    {
        try {
            $this->setupMidtransConfig();
            $response = Transaction::cancel($orderId);
            return [
                'status' => 'success',
                'data' => $response,
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Refund transaksi
     */
    public function refundTransaction($orderId, $refundAmount = null)
    {
        try {
            $this->setupMidtransConfig();
            if ($refundAmount) {
                $response = Transaction::refund($orderId, ['refund_amount' => $refundAmount]);
            } else {
                $response = Transaction::refund($orderId);
            }
            return [
                'status' => 'success',
                'data' => $response,
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
}
