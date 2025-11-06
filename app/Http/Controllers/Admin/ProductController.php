<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        // Delete images
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus');
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
        $variant->delete();
        return redirect()->back()->with('success', 'Varian berhasil dihapus');
    }

    public function deleteImage(ProductImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return redirect()->back()->with('success', 'Gambar berhasil dihapus');
    }

}