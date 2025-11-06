<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::with(['primaryImage', 'category'])
            ->latest()
            ->take(8)
            ->get();
        
        $categories = Category::withCount('products')->get();

        return view('home', compact('products', 'categories'));
    }
}