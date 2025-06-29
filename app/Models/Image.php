<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
        'store_id',
        'name',
        'path',
        'original_name',
        'mime_type',
        'size'
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function getFullPathAttribute()
    {
        return asset('storage/' . $this->path);
    }
}
