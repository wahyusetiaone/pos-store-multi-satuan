<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductImage;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        foreach ($products as $product) {
            // Add 1-3 dummy images per product
            $count = rand(1, 3);
            for ($i = 0; $i < $count; $i++) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => 'gallery/gallery-img' . rand(1,10) . '.png',
                ]);
            }
        }
    }
}

