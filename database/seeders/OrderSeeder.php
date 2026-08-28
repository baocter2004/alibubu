<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::with('variants')->get();
        $users = User::with('userAddresses')->get();

        if ($products->isEmpty() || $users->isEmpty()) {
            return;
        }

        foreach ($users as $index => $user) {
            if ($user->orders()->exists()) {
                continue;
            }

            $address = $user->userAddresses->first();
            $selected = $products->random(min(2, $products->count()));

            $total = $selected->sum(fn ($product) => (float) $product->effective_price);

            $order = Order::create([
                'code' => 'ORD' . now()->format('ymd') . Str::upper(Str::random(5)),
                'user_id' => $user->id,
                'phone_number' => $user->phone_number ?? '0900000000',
                'email' => $user->email,
                'fullname' => $user->fullname,
                'address' => $address?->full_address ?? 'Việt Nam',
                'note' => null,
                'total_amount' => $total,
                'is_paid' => $index % 2 === 0,
            ]);

            foreach ($selected as $product) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->effective_price,
                    'old_price' => $product->base_price,
                    'quantity' => 1,
                ]);
            }
        }
    }
}
