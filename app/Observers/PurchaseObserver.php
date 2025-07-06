<?php

namespace App\Observers;

use App\Models\Purchase;
use App\Models\Shipping;
use App\Models\ShippingItem;
use Illuminate\Support\Str;

class PurchaseObserver
{
    public function created(Purchase $purchase)
    {
        // Jika status completed, update stock & buy_price produk
        if ($purchase->status === 'completed') {
            foreach ($purchase->items as $item) {
                $conversion = $item->productUnit ? $item->productUnit->conversion_factor : 1;
                $qtyToAdd = $item->quantity * $conversion;
                if ($item->product) {
                    $item->product->update([
                        'buy_price' => $item->buy_price,
                        'stock' => $item->product->stock + $qtyToAdd
                    ]);
                }
            }
        }
        // Jika status shipped, buat Shipping otomatis
        if ($purchase->status === 'shipped') {
            // Generate number_shipping dari timestamp
            $numberShipping = 'SHP-' . now()->format('YmdHis');

            $shipping = Shipping::create([
                'store_id' => $purchase->store_id,
                'user_id' => $purchase->user_id,
                'number_shipping' => $numberShipping,
                'shipping_date' => $purchase->purchase_date,
                'supplier_id' => $purchase->supplier_id,
                'total' => $purchase->total,
                'status' => 'shipped',
                'ship_date' => $purchase->ship_date,
                'note' => $purchase->note,
            ]);

            // Copy items
            foreach ($purchase->items as $item) {
                ShippingItem::create([
                    'shipping_id' => $shipping->id,
                    'product_id' => $item->product_id,
                    'product_unit_id' => $item->product_unit_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'buy_price' => $item->buy_price,
                    'subtotal' => $item->subtotal,
                    'ppn' => $item->ppn,
                ]);
            }
        }
    }
}
