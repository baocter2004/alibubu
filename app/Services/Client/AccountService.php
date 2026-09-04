<?php

namespace App\Services\Client;

use App\Const\OrderConst;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountService
{
    public function orders(User $user, array $params = [], int $limit = 10): LengthAwarePaginator
    {
        return $user->orders()
            ->withCount('items')
            ->when(! empty($params['status']), fn ($query) => $query->where('status', (int) $params['status']))
            ->when(! empty($params['keyword']), fn ($query) => $query->where('code', 'like', '%' . $params['keyword'] . '%'))
            ->latest('id')
            ->paginate($limit);
    }

    public function findOrder(User $user, int|string $id): ?Order
    {
        return $user->orders()
            ->with(['items.product', 'items.productVariant'])
            ->whereKey($id)
            ->first();
    }

    public function cancelOrder(User $user, int|string $id, ?string $reason = null): array
    {
        $order = $user->orders()->whereKey($id)->first();

        if (! $order) {
            return [
                'status' => false,
                'message' => __('client.account.messages.order_not_found'),
            ];
        }

        if (! OrderConst::isCancellableByCustomer($order->status)) {
            return [
                'status' => false,
                'message' => __('client.account.messages.cancel_not_allowed'),
            ];
        }

        DB::transaction(function () use ($order, $reason) {
            $this->restoreStock($order);

            $order->update([
                'status' => OrderConst::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'cancel_reason' => $reason,
            ]);
        });

        return [
            'status' => true,
            'message' => __('client.account.messages.order_cancelled'),
        ];
    }

    protected function restoreStock(Order $order): void
    {
        foreach ($order->items()->get() as $item) {
            if (! $item->product_id) {
                continue;
            }

            Product::whereKey($item->product_id)->update([
                'stock' => DB::raw('stock + ' . (int) $item->quantity),
                'sold' => DB::raw('MAX(sold - ' . (int) $item->quantity . ', 0)'),
            ]);
        }
    }

    public function updateProfile(User $user, array $params): User
    {
        $user->update([
            'fullname' => $params['fullname'],
            'phone_number' => $params['phone_number'] ?? null,
            'gender' => $params['gender'] ?? null,
            'birthday' => $params['birthday'] ?? null,
        ]);

        return $user->refresh();
    }

    public function updatePassword(User $user, string $password): void
    {
        $user->update(['password' => Hash::make($password)]);
    }

    public function storeAddress(User $user, array $params): UserAddress
    {
        return DB::transaction(function () use ($user, $params) {
            $address = $user->userAddresses()->create($this->addressAttributes($params));

            $this->syncDefault($user, $address, ! empty($params['is_default']));

            return $address;
        });
    }

    public function updateAddress(User $user, int|string $id, array $params): ?UserAddress
    {
        $address = $user->userAddresses()->whereKey($id)->first();

        if (! $address) {
            return null;
        }

        return DB::transaction(function () use ($user, $address, $params) {
            $address->update($this->addressAttributes($params));

            $this->syncDefault($user, $address, ! empty($params['is_default']));

            return $address->refresh();
        });
    }

    public function deleteAddress(User $user, int|string $id): bool
    {
        $address = $user->userAddresses()->whereKey($id)->first();

        if (! $address) {
            return false;
        }

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $user->userAddresses()->oldest('id')->first()?->update(['is_default' => true]);
        }

        return true;
    }

    protected function addressAttributes(array $params): array
    {
        $province = \App\Models\Province::find($params['province_id'] ?? null);
        $ward = \App\Models\Ward::find($params['ward_id'] ?? null);

        return [
            'fullname' => $params['fullname'],
            'phone_number' => $params['phone_number'],
            'province_id' => $province?->id,
            'ward_id' => $ward?->id,
            'province' => $province?->name,
            'ward' => $ward?->name,
            'address' => $params['address'],
        ];
    }

    protected function syncDefault(User $user, UserAddress $address, bool $makeDefault): void
    {
        $isOnly = $user->userAddresses()->count() === 1;

        if (! $makeDefault && ! $isOnly) {
            $address->update(['is_default' => false]);

            return;
        }

        $user->userAddresses()->whereKeyNot($address->id)->update(['is_default' => false]);
        $address->update(['is_default' => true]);
    }
}
