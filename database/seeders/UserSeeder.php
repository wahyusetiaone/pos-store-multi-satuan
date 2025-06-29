<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // 1. Create Owner User (can access all stores)
        $owner = User::factory()->create([
            'name' => 'Owner Yuka Kelontong',
            'email' => 'owner@yuka.com',
            'role' => 'owner',
            'password' => Hash::make('admin1234'),
        ]);

        // 2. Create Purchasing User (can access all stores)
        $purchasing = User::factory()->create([
            'name' => 'Purchasing Yuka',
            'email' => 'purchase@yuka.com',
            'role' => 'purchasing',
            'password' => Hash::make('admin1234'),
        ]);

        // 3. Create the single store: Yuka Kelontong
        $yukaKelontongStore = Store::create([
            'name' => 'Yuka Kelontong',
            'address' => 'Jl. Kebahagiaan No. 10, Jakarta',
            'phone' => '021-98765432',
            'email' => 'yuka.kelontong@example.com',
            'description' => 'Toko kelontong serba ada Yuka',
            'is_active' => true,
        ]);

        // 4. Create Store Admin for Yuka Kelontong
        $storeAdminYuka = User::factory()->create([
            'name' => 'Admin Yuka Kelontong',
            'email' => 'admin@yuka.com',
            'role' => 'store_admin',
            'password' => Hash::make('admin1234'),
        ]);

        // 5. Create Cashier for Yuka Kelontong
        $cashierYuka = User::factory()->create([
            'name' => 'Kasir Yuka Kelontong',
            'email' => 'kasir@yuka.com',
            'role' => 'cashier',
            'password' => Hash::make('admin1234'),
        ]);

        // 6. Attach Store Admin to Yuka Kelontong
        // The pivot table (likely `store_user`) will link them.
        $yukaKelontongStore->users()->attach($storeAdminYuka->id);
        // Set the current_store_id for the admin
        $storeAdminYuka->update(['current_store_id' => $yukaKelontongStore->id]);

        // 7. Attach Cashier to Yuka Kelontong
        $yukaKelontongStore->users()->attach($cashierYuka->id);
        // Set the current_store_id for the cashier
        $cashierYuka->update(['current_store_id' => $yukaKelontongStore->id]);

        // Note: Owner and Purchasing roles typically have access to all stores
        // and do not need to be explicitly attached to a specific 'current_store_id'
        // in this seeding context, as their role dictates their access.
    }
}
