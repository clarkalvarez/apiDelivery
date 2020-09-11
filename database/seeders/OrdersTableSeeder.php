<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $orders = [
            [
                'name' => 'Mango',
                'available_stock' => 5, 
            ],
            [
                'name' => 'Strawberry',
                'available_stock' => 2, 
            ],
            [
                'name' => 'Apply',
                'available_stock' => 0, 
            ],
        ];

        foreach ($orders as $order) {
            \App\Models\Order::create([
                'name' => $order['name'],
                'available_stock' => $order['available_stock'], 
            ]);
        }
 
 
    }
}
