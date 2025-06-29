<?php

namespace App\Observers;

use App\Models\ShippingItem;

class ShippingItemObserver
{
    public function updating(ShippingItem $shippingItem)
    {
        // Check if qty_received is being updated and not null
        if ($shippingItem->isDirty('qty_received') && !is_null($shippingItem->qty_received)) {
            // Konversi qty_received ke satuan dasar produk
            $conversion = $shippingItem->productUnit ? $shippingItem->productUnit->conversion_factor : 1;
            $qtyToAdd = $shippingItem->qty_received * $conversion;
            $shippingItem->product->update([
                'buy_price' => $shippingItem->buy_price,
                'stock' => $shippingItem->product->stock + $qtyToAdd
            ]);
        }
    }
}
