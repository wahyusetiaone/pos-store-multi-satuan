<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class ProductImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $store_id;

    public function __construct($store_id)
    {
        $this->store_id = $store_id;
    }

    public function model(array $row)
    {
        // Convert category name to lowercase
        $categoryName = Str::lower($row['category']);

        // Find or create the category
        $category = Category::firstOrCreate(
            [
                'store_id' => $this->store_id,
                'name' => $categoryName
            ],
            [
                'description' => null,
                'status' => true
            ]
        );

        return new Product([
            'store_id' => $this->store_id,
            'category_id' => $category->id,
            'name' => $row['name'],
            'sku' => $row['sku'],
            'buy_price' => $row['buy_price'],
            'price' => $row['price'],
            'stock' => $row['stock'],
            'description' => $row['description'] ?? null,
            'status' => $row['status'] ?? true,
        ]);
    }

    public function rules(): array
    {
        return [
            'category' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'buy_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ];
    }
}
