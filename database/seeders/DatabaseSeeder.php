<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'username' => 'admin',
            'email' => 'admin@yaka.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
            'address' => 'Jl. Admin No. 1'
        ]);

        // Create Regular User
        User::create([
            'username' => 'user',
            'email' => 'user@yaka.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'phone' => '081234567891',
            'address' => 'Jl. User No. 2'
        ]);

        // Create Store Settings
        StoreSetting::create([
            'store_name' => 'Yaka Store',
            'email' => 'info@yaka.com',
            'phone' => '081234567890',
            'address' => 'Jl. Yaka No. 123, Surakarta, Jawa Tengah'
        ]);

        // Create Categories
        $categories = [
            'Kaos',
        ];

        foreach ($categories as $categoryName) {
            Category::create(['name' => $categoryName]);
        }

        // Create Sample Products
        $products = [
            [
                'name' => 'Kaos Polos Premium',
                'category_id' => 1,
                'price' => 75000,
                'description' => 'Kaos polos berkualitas tinggi dengan bahan cotton combed 30s yang nyaman dan tidak mudah luntur.',
                'details' => 'Bahan: Cotton Combed 30s, Sablon: Plastisol, Jahitan: Obras Rapi',
                'stock' => 100
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create($productData);

            // Add variants
            $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
            $colors = ['Hitam', 'Putih', 'Navy', 'Abu-abu'];

            foreach ($sizes as $size) {
                foreach (array_slice($colors, 0, 2) as $color) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'size' => $size,
                        'color' => $color,
                        'stock' => rand(5, 20)
                    ]);
                }
            }
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin credentials: admin@yaka.com / password');
        $this->command->info('User credentials: user@yaka.com / password');
    }
}