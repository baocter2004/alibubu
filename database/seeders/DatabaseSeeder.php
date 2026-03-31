<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

use App\Const\UserConst;
use App\Const\ProductConst;
use App\Const\CouponConst;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        //============ 1. ADMINS & USERS ============
        $userIds = [];
        for ($i = 0; $i < 5; $i++) {
            $uid = DB::table('users')->insertGetId([
                'fullname' => $faker->name,
                'email' => $i == 0 ? 'user@gmail.com' : $faker->unique()->safeEmail,
                'password' => Hash::make('password'),
                'role' => UserConst::ROLE_USER,
                'phone_number' => '09' . rand(10000000, 99999999),
                'status' => 1,
                'created_at' => now(),
            ]);
            $userIds[] = $uid;

            DB::table('user_addresses')->insert([
                'user_id' => $uid,
                'fullname' => $faker->name,
                'phone_number' => '0987654321',
                'address' => $faker->address,
                'is_default' => true,
            ]);
        }

        //============ 2. CATEGORIES & BRANCHES & TAGS ============
        $branchIds = [];
        foreach (['Apple', 'Samsung', 'Sony'] as $b) {
            $branchIds[] = DB::table('branches')->insertGetId(['name' => $b, 'slug' => Str::slug($b), 'logo' => 'logo.png', 'is_active' => 1]);
        }

        $categoryIds = [];
        foreach (['Smartphones', 'Laptops', 'Tablets'] as $c) {
            $categoryIds[] = DB::table('categories')->insertGetId(['name' => $c, 'slug' => Str::slug($c), 'is_active' => 1]);
        }

        $tagIds = [];
        foreach (['Trending', 'Flash Sale'] as $t) {
            $tagIds[] = DB::table('tags')->insertGetId(['name' => $t, 'slug' => Str::slug($t)]);
        }

        //============ 3. ATTRIBUTES & VALUES ============
        $colorId = DB::table('attributes')->insertGetId(['name' => 'Color', 'slug' => 'color', 'is_active' => 1]);
        $colorValIds = [];
        foreach (['Red', 'Blue', 'Black'] as $v) {
            $colorValIds[] = DB::table('attribute_values')->insertGetId(['attribute_id' => $colorId, 'value' => $v, 'is_active' => 1]);
        }

        //============ 4. PRODUCTS & VARIANTS & GALLERIES ============
        $productIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $name = "Product Premium " . $i;
            $pid = DB::table('products')->insertGetId([
                'branch_id' => $faker->randomElement($branchIds),
                'name' => $name,
                'slug' => Str::slug($name),
                'thumbnail' => 'thumb.jpg',
                'type' => ProductConst::VARIANT,
                'is_sale' => 1, 'is_featured' => 1, 'is_trending' => 1, 'is_active' => 1,
                'created_at' => now(),
            ]);
            $productIds[] = $pid;

            // Gallery ảnh
            DB::table('product_galleries')->insert([
                ['product_id' => $pid, 'image' => 'img1.jpg'],
                ['product_id' => $pid, 'image' => 'img2.jpg'],
            ]);

            // Pivot Category & Tag
            DB::table('category_product')->insert(['product_id' => $pid, 'category_id' => $faker->randomElement($categoryIds)]);
            DB::table('product_tag')->insert(['product_id' => $pid, 'tag_id' => $faker->randomElement($tagIds)]);

            // Tạo Variant
            foreach ($colorValIds as $vId) {
                $vid = DB::table('product_variants')->insertGetId([
                    'product_id' => $pid,
                    'sku' => strtoupper(Str::random(10)),
                    'price' => 20000000,
                    'sale_price' => 18000000,
                    'thumbnail' => 'variant.jpg',
                    'is_active' => 1,
                ]);
                DB::table('attribute_value_product_variant')->insert(['product_variant_id' => $vid, 'attribute_value_id' => $vId]);
            }
        }

        //============ 5. PRODUCT ACCESSORIES (SẢN PHẨM ĐI KÈM) ============
        DB::table('product_accessory')->insert([
            'product_id' => $productIds[0],
            'product_accessory_id' => $productIds[1]
        ]);

        //============ 6. COUPONS & RESTRICTIONS ============
        $cpId = DB::table('coupons')->insertGetId([
            'code' => 'HELLOSUMMER',
            'title' => 'Summer Sale',
            'discount_type' => CouponConst::FIX_AMOUNT,
            'discount_value' => 50000,
            'usage_limit' => 100,
            'usage_count' => 0,
            'is_active' => 1,
            'is_expired' => 0,
            'created_at' => now(),
        ]);

        DB::table('coupon_restrictions')->insert([
            'coupon_id' => $cpId,
            'min_order_value' => 200000,
            'valid_categories' => json_encode([$categoryIds[0]]),
        ]);

        //============ 7. ORDERS & CART ============
        foreach ($userIds as $uid) {
            $orderId = DB::table('orders')->insertGetId([
                'code' => 'ORD-' . strtoupper(Str::random(8)),
                'user_id' => $uid,
                'phone_number' => '0900000000',
                'fullname' => 'Guest User',
                'email' => 'guest@gmail.com',
                'address' => 'Vietnam',
                'total_amount' => 18000000,
                'is_paid' => 1,
                'created_at' => now(),
            ]);

            DB::table('order_items')->insert([
                'order_id' => $orderId,
                'product_id' => $productIds[0],
                'quantity' => 1,
                'price' => 18000000,
                'name' => 'Product Premium Sample',
            ]);

            // Giỏ hàng mẫu (Cart Items)
            DB::table('cart_items')->insert([
                'product_id' => $productIds[1],
                'quantity' => 2,
                'created_at' => now(),
            ]);
        }
    }
}