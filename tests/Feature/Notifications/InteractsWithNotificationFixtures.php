<?php

namespace Tests\Feature\Notifications;

use App\Const\AdminConst;
use App\Const\OrderConst;
use App\Models\Admin;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\Admin\RoleService;
use Illuminate\Support\Str;

trait InteractsWithNotificationFixtures
{
    protected function makeBranch(): Branch
    {
        return Branch::create([
            'name' => 'Test Branch',
            'slug' => 'test-branch-' . Str::random(8),
            'logo' => 'branches/test.png',
            'is_active' => true,
        ]);
    }

    protected function makeProduct(array $attributes = []): Product
    {
        return Product::create(array_merge([
            'branch_id' => $this->makeBranch()->id,
            'name' => 'Test Product ' . Str::random(8),
            'slug' => 'test-product-' . Str::random(8),
            'is_sale' => false,
            'is_featured' => false,
            'is_trending' => false,
            'is_active' => true,
        ], $attributes));
    }

    protected function makeCustomer(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'fullname' => 'Test Customer',
        ], $attributes));
    }

    protected function makeAdmin(int $role = AdminConst::ROLE_MANAGER, array $attributes = []): Admin
    {
        return Admin::factory()->create(array_merge([
            'role' => $role,
        ], $attributes));
    }

    protected function grantRolePermissions(array $matrix): void
    {
        app(RoleService::class)->update($matrix);
    }

    protected function makeCompletedOrder(User $user, Product $product): Order
    {
        $order = Order::create([
            'code' => 'ORD' . Str::upper(Str::random(8)),
            'user_id' => $user->id,
            'phone_number' => '0900000000',
            'email' => $user->email,
            'fullname' => $user->fullname,
            'address' => '123 Test Street',
            'total_amount' => 100000,
            'status' => OrderConst::STATUS_COMPLETED,
            'is_paid' => true,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'name' => $product->name,
            'price' => 100000,
            'quantity' => 1,
        ]);

        return $order;
    }
}
