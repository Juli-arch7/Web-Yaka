<?php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::with(['product.primaryImage', 'variant'])
            ->where('user_id', auth()->id())
            ->get();

        $total = $carts->sum(function($cart) {
            return $cart->subtotal();
        });

        return view('cart.index', compact('carts', 'total'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::where('user_id', auth()->id())
            ->where('product_id', $validated['product_id'])
            ->where('product_variant_id', $validated['variant_id'] ?? null)
            ->first();

        if ($cart) {
            $cart->increment('quantity', $validated['quantity']);
        } else {
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $validated['product_id'],
                'product_variant_id' => $validated['variant_id'] ?? null,
                'quantity' => $validated['quantity']
            ]);
        }

        return redirect()->back()->with('success', 'Produk ditambahkan ke keranjang');
    }

    public function update(Request $request, Cart $cart)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart->update($validated);

        return redirect()->back()->with('success', 'Keranjang diperbarui');
    }

    public function destroy(Cart $cart)
    {
        $cart->delete();

        return redirect()->back()->with('success', 'Produk dihapus dari keranjang');
    }
}