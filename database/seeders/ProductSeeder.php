<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Store;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $stores = Store::all();

        foreach ($stores as $store) {
            // Get categories for this store
            $categories = Category::where('store_id', $store->id)->pluck('id')->toArray();

            if (!empty($categories)) {
                Product::factory()
                    ->count(10)
                    ->create([
                        'store_id' => $store->id,
                        'category_id' => function () use ($categories) {
                            return $categories[array_rand($categories)];
                        },
                    ]);
            }
        }
    }
}
