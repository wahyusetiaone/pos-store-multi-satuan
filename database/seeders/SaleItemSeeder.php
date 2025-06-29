<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;

class SaleItemSeeder extends Seeder
{
    public function run(): void
    {
        $sales = Sale::all();

        foreach ($sales as $sale) {
            // Get products from the same store
            $products = Product::where('store_id', $sale->store_id)->pluck('id')->toArray();

            if (!empty($products)) {
                $itemCount = rand(1, 4);
                $usedProducts = [];

                for ($i = 0; $i < $itemCount; $i++) {
                    $productId = $products[array_rand($products)];
                    // Avoid duplicate product in same sale
                    if (in_array($productId, $usedProducts)) continue;
                    $usedProducts[] = $productId;

                    $quantity = rand(1, 5);
                    $product = Product::find($productId);
                    $price = $product->price;
                    $discount = rand(0, round($price * 0.1)); // Discount max 10% of price

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'price' => $price,
                        'discount' => $discount,
                        'subtotal' => ($quantity * $price) - ($quantity * $discount)
                    ]);
                }
            }
        }
    }
}
