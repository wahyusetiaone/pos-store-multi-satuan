<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Finance;
use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Carbon;

class FinanceSeeder extends Seeder
{
    public function run(): void
    {
        $stores = Store::all();

        foreach ($stores as $store) {
            // Get users who have access to this store
            $users = $store->users()->pluck('users.id')->toArray();

            if (!empty($users)) {
                // Create finances for each store
                for ($i = 0; $i < 10; $i++) {
                    Finance::create([
                        'store_id' => $store->id,
                        'user_id' => $users[array_rand($users)],
                        'date' => Carbon::now()->subDays(rand(0, 30)),
                        'type' => ['income', 'expense'][array_rand([0,1])],
                        'category' => ['Sales', 'Purchase', 'Salary', 'Other'][array_rand([0,1,2,3])],
                        'amount' => rand(10000, 200000),
                        'description' => 'Test finance transaction'
                    ]);
                }
            }
        }
    }
}
