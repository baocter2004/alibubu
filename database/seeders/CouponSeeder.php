<?php

namespace Database\Seeders;

use App\Const\CouponConst;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\CouponRestriction;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryIds = Category::query()->pluck('id')->take(2)->all();

        $coupons = [
            [
                'code' => 'ALIBUBU50K',
                'title' => 'Giảm 50.000đ',
                'description' => 'Áp dụng cho đơn hàng từ 500.000đ',
                'discount_type' => CouponConst::FIX_AMOUNT,
                'discount_value' => 50000,
                'usage_limit' => 500,
                'min_order_value' => 500000,
                'max_discount_value' => null,
            ],
            [
                'code' => 'SALE10',
                'title' => 'Giảm 10%',
                'description' => 'Giảm 10% tối đa 1.000.000đ',
                'discount_type' => CouponConst::PERCENT,
                'discount_value' => 10,
                'usage_limit' => 200,
                'min_order_value' => 1000000,
                'max_discount_value' => 1000000,
            ],
        ];

        foreach ($coupons as $item) {
            $coupon = Coupon::updateOrCreate(
                ['code' => $item['code']],
                [
                    'title' => $item['title'],
                    'description' => $item['description'],
                    'discount_type' => $item['discount_type'],
                    'discount_value' => $item['discount_value'],
                    'usage_limit' => $item['usage_limit'],
                    'usage_count' => 0,
                    'is_expired' => false,
                    'is_active' => true,
                    'start_date' => now()->subDay(),
                    'end_date' => now()->addMonth(),
                ]
            );

            CouponRestriction::updateOrCreate(
                ['coupon_id' => $coupon->id],
                [
                    'min_order_value' => $item['min_order_value'],
                    'max_discount_value' => $item['max_discount_value'],
                    'valid_categories' => $categoryIds,
                    'valid_products' => null,
                ]
            );
        }
    }
}
