<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Store;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $stores = Store::pluck('id')->toArray();

        foreach ($stores as $storeId) {
            Customer::factory()
                ->count(5)
                ->create([
                    'store_id' => $storeId
                ]);
        }
    }
}
