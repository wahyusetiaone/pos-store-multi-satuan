<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Store;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $stores = Store::pluck('id')->toArray();

        $categories = [
            ['name' => 'Keyboard', 'description' => 'Input device for typing'],
            ['name' => 'Mouse', 'description' => 'Pointing device for computers'],
            ['name' => 'Monitor', 'description' => 'Display screen for computers'],
            ['name' => 'Printer', 'description' => 'Device for printing documents'],
        ];

        foreach ($categories as $cat) {
            foreach ($stores as $storeId) {
                Category::create([
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                    'store_id' => $storeId
                ]);
            }
        }
    }
}
