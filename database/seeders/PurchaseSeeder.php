<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Carbon;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $stores = Store::all();

        foreach ($stores as $store) {
            // Get users who have access to this store
            $users = $store->users()->pluck('users.id')->toArray();

            if (!empty($users)) {
                // Create 5 purchases per store
                for ($i = 0; $i < 5; $i++) {
                    Purchase::create([
                        'store_id' => $store->id,
                        'user_id' => $users[array_rand($users)],
                        'purchase_date' => Carbon::now()->subDays(rand(0, 30)),
                        'supplier' => 'Supplier ' . rand(1, 10),
                        'total' => rand(10000, 300000),
                        'note' => 'Test purchase',
                        'status' => ['drafted', 'shipped', 'completed'][array_rand([0,1,2])]
                    ]);
                }
            }
        }
    }
}
