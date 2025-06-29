<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'store_id',
        'code',
        'discount_amount',
        'valid_from',
        'valid_until',
        'usage_limit',
        'times_used',
        'is_active'
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function isValid()
    {
        $now = now();

        $result = $this->is_active &&
            $now->greaterThanOrEqualTo($this->valid_from) &&
            ($this->valid_until === null || $now->lessThanOrEqualTo($this->valid_until)) &&
            ($this->usage_limit === null || $this->times_used < $this->usage_limit);
        return $result;
    }
}
