<?php

namespace App\Observers;

use App\Models\ShippingItem;

class ShippingItemObserver
{
    public function updating(ShippingItem $shippingItem)
    {
        // Check if qty_received is being updated and not null
        if ($shippingItem->isDirty('qty_received') && !is_null($shippingItem->qty_received)) {
            $shippingItem->product->update([
                'buy_price' => $shippingItem->buy_price
            ]);
        }
    }
}
