<?php

namespace Database\Seeders;

use App\Const\OrderConst;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::query()->limit(4)->get();
        $products = Product::query()->limit(20)->get();

        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }

        $comments = [
            5 => ['Sản phẩm dùng rất tốt, giao hàng nhanh.', 'Excellent quality, exactly as described.'],
            4 => ['Máy đẹp, pin ổn, giá hợp lý.', 'Good value, minor packaging issue.'],
            3 => ['Tạm ổn so với tầm giá.', 'Decent but nothing special.'],
        ];

        foreach ($products as $index => $product) {
            $reviewers = $users->slice(0, ($index % 3) + 1);

            foreach ($reviewers as $offset => $user) {
                $rating = [5, 4, 5, 3, 4][($index + $offset) % 5];

                ProductReview::updateOrCreate(
                    ['product_id' => $product->id, 'user_id' => $user->id],
                    [
                        'order_id' => $user->orders()->where('status', OrderConst::STATUS_COMPLETED)->value('id'),
                        'rating' => $rating,
                        'title' => $rating >= 4 ? 'Rất hài lòng' : 'Ổn trong tầm giá',
                        'comment' => $comments[max($rating, 3)][($index + $offset) % 2],
                        'is_approved' => ($index + $offset) % 4 !== 0,
                        'approved_at' => ($index + $offset) % 4 !== 0 ? now() : null,
                    ]
                );
            }

            $product->refreshRating();
        }
    }
}
