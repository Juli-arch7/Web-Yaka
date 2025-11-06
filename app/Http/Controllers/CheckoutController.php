<?php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $carts = Cart::with(['product', 'variant'])
            ->where('user_id', auth()->id())
            ->get();

        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong');
        }

        $subtotal = $carts->sum(function($cart) {
            return $cart->subtotal();
        });

        return view('checkout.index', compact('carts', 'subtotal'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipping_address' => 'required',
            'shipping_method' => 'required',
            'shipping_cost' => 'required|numeric',
            'payment_method' => 'required'
        ]);

        $carts = Cart::where('user_id', auth()->id())->get();

        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong');
        }

        $subtotal = $carts->sum(function($cart) {
            return $cart->subtotal();
        });

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => auth()->id(),
                'shipping_address' => $validated['shipping_address'],
                'shipping_method' => $validated['shipping_method'],
                'shipping_cost' => $validated['shipping_cost'],
                'payment_method' => $validated['payment_method'],
                'subtotal' => $subtotal,
                'discount' => 0,
                'total' => $subtotal + $validated['shipping_cost'],
                'status' => 'pending'
            ]);

            foreach ($carts as $cart) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'product_variant_id' => $cart->product_variant_id,
                    'product_name' => $cart->product->name,
                    'price' => $cart->product->price,
                    'quantity' => $cart->quantity,
                    'subtotal' => $cart->subtotal()
                ]);
            }

            Cart::where('user_id', auth()->id())->delete();

            DB::commit();

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Pesanan berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}