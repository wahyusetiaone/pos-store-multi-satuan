<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;

class PurchaseItemSeeder extends Seeder
{
    public function run(): void
    {
        $purchases = Purchase::all();

        foreach ($purchases as $purchase) {
            // Get products from the same store
            $products = Product::where('store_id', $purchase->store_id)->pluck('id')->toArray();

            if (!empty($products)) {
                $itemCount = rand(1, 4);
                $usedProducts = [];

                for ($i = 0; $i < $itemCount; $i++) {
                    $productId = $products[array_rand($products)];
                    // Avoid duplicate product in same purchase
                    if (in_array($productId, $usedProducts)) continue;
                    $usedProducts[] = $productId;

                    $quantity = rand(1, 10);
                    $price = rand(1000, 100000);
                    $buy_price = rand(1000, $price); // buy_price should be less than selling price

                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'price' => $price,
                        'buy_price' => $buy_price,
                        'subtotal' => $quantity * $buy_price,
                    ]);
                }
            }
        }
    }
}
