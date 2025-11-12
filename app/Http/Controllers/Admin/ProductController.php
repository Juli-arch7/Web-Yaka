<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'primaryImage']);

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required',
            'price' => 'required|numeric',
            'description' => 'required',
            'details' => 'nullable',
            'stock' => 'required|integer',
            'images.*' => 'nullable|image|max:2048'
        ]);

        $product = Product::create($validated);

        // Upload images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => $index === 0
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $product->load(['images', 'variants']);
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required',
            'price' => 'required|numeric',
            'description' => 'required',
            'details' => 'nullable',
            'stock' => 'required|integer',
            'images.*' => 'nullable|image|max:2048'
        ]);

        // Update slug jika nama berubah
        if ($product->name !== $validated['name']) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        }

        $product->update($validated);

        // Upload new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => false
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();

            // Check if product has orders
            $hasOrders = \App\Models\OrderItem::where('product_id', $product->id)->exists();
            
            if ($hasOrders) {
                return redirect()->back()
                    ->with('error', 'Produk tidak dapat dihapus karena sudah ada dalam pesanan. Anda bisa mengubah stok menjadi 0 untuk menonaktifkan produk.');
            }

            // Delete images from storage
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }

            // Delete variants
            $product->variants()->delete();

            // Delete reviews
            $product->reviews()->delete();

            // Delete from cart
            \App\Models\Cart::where('product_id', $product->id)->delete();

            // Finally delete the product
            $product->delete();

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil dihapus');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }

    public function variants(Product $product)
    {
        $product->load('variants');
        return view('admin.products.variants', compact('product'));
    }

    public function storeVariant(Request $request, Product $product)
    {
        $validated = $request->validate([
            'size' => 'required',
            'color' => 'required',
            'stock' => 'required|integer'
        ]);

        $product->variants()->create($validated);

        return redirect()->back()->with('success', 'Varian berhasil ditambahkan');
    }

    public function updateVariant(Request $request, ProductVariant $variant)
    {
        $validated = $request->validate([
            'size' => 'required',
            'color' => 'required',
            'stock' => 'required|integer'
        ]);

        $variant->update($validated);

        return redirect()->back()->with('success', 'Varian berhasil diperbarui');
    }

    public function destroyVariant(ProductVariant $variant)
    {
        try {
            // Check if variant used in orders
            $hasOrders = \App\Models\OrderItem::where('product_variant_id', $variant->id)->exists();
            
            if ($hasOrders) {
                return redirect()->back()
                    ->with('error', 'Varian tidak dapat dihapus karena sudah ada dalam pesanan.');
            }

            // Delete from cart
            \App\Models\Cart::where('product_variant_id', $variant->id)->delete();

            $variant->delete();
            
            return redirect()->back()->with('success', 'Varian berhasil dihapus');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus varian: ' . $e->getMessage());
        }
    }

    public function deleteImage(ProductImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return redirect()->back()->with('success', 'Gambar berhasil dihapus');
    }
}
