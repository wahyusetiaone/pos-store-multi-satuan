<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
//            CategorySeeder::class,
//            CustomerSeeder::class,
//            ProductSeeder::class,
//            ProductImageSeeder::class,
//            SaleSeeder::class,
//            SaleItemSeeder::class,
//            PurchaseSeeder::class,
//            PurchaseItemSeeder::class,
//            FinanceSeeder::class,
        ]);
    }
}
