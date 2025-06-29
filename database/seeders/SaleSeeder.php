<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Carbon;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        $stores = Store::all();

        foreach ($stores as $store) {
            // Get customers for this store
            $customers = Customer::where('store_id', $store->id)->pluck('id')->toArray();
            // Get users who have access to this store
            $users = $store->users()->pluck('users.id')->toArray();

            if (!empty($customers) && !empty($users)) {
                // Create 10 sales per store
                for ($i = 0; $i < 10; $i++) {
                    Sale::create([
                        'store_id' => $store->id,
                        'customer_id' => $customers[array_rand($customers)],
                        'user_id' => $users[array_rand($users)],
                        'sale_date' => Carbon::now()->subDays(rand(0, 30)),
                        'total' => rand(10000, 500000),
                        'discount' => rand(0, 5000),
                        'paid' => rand(10000, 500000),
                        'payment_method' => ['cash', 'transfer', 'ewallet'][array_rand([0,1,2])],
                        'note' => 'Test sale',
                        'status' => ['drafted', 'completed', 'cancelled'][array_rand([0,1,2])]
                    ]);
                }
            }
        }
    }
}
