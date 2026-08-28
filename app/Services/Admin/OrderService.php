<?php

namespace App\Services\Admin;

use App\Const\OrderConst;
use App\Models\Order;
use App\Models\Product;
use App\Repositories\OrderRepository;
use App\Services\BaseCrudService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService extends BaseCrudService
{
    protected function getRepository(): OrderRepository
    {
        if (empty($this->repository)) {
            $this->repository = app()->make(OrderRepository::class);
        }

        return $this->repository;
    }

    protected function buildFilterParams(array $params = []): array
    {
        $wheres = Arr::get($params, 'wheres', []);
        $whereIns = Arr::get($params, 'where_ins', []);
        $whereLikes = Arr::get($params, 'where_likes', []);
        $whereEquals = Arr::get($params, 'where_equals', []);
        $whereBetweens = Arr::get($params, 'where_betweens', []);
        $orWheres = Arr::get($params, 'or_wheres', []);
        $sort = Arr::get($params, 'sort', 'id:desc');
        $relates = Arr::get($params, 'relates', []);
        $relatesCount = Arr::get($params, 'relates_count', []);

        if (! empty($params['code'])) {
            $whereLikes['code'] = $params['code'];
        }

        if (! empty($params['status'])) {
            $wheres['status'] = (int) $params['status'];
        }

        if (isset($params['is_paid']) && $params['is_paid'] !== '' && $params['is_paid'] !== null) {
            $wheres['is_paid'] = (int) $params['is_paid'];
        }

        if (! empty($params['from_date']) && ! empty($params['to_date'])) {
            $whereBetweens['created_at'] = [
                $params['from_date'] . ' 00:00:00',
                $params['to_date'] . ' 23:59:59',
            ];
        }

        if (! empty($params['keyword'])) {
            $orWheres[] = ['code', 'like', '%' . $params['keyword'] . '%'];
            $orWheres[] = ['fullname', 'like', '%' . $params['keyword'] . '%'];
            $orWheres[] = ['phone_number', 'like', '%' . $params['keyword'] . '%'];
            $orWheres[] = ['email', 'like', '%' . $params['keyword'] . '%'];
        }

        return [
            'wheres' => $wheres,
            'where_equals' => $whereEquals,
            'or_wheres' => $orWheres,
            'where_likes' => $whereLikes,
            'where_ins' => $whereIns,
            'where_betweens' => $whereBetweens,
            'sort' => $sort,
            'relates' => $relates,
            'relates_count' => $relatesCount,
        ];
    }

    public function updateStatus(int|string $id, int $status, ?string $reason = null): array
    {
        try {
            $order = $this->find($id);

            if (! $order) {
                return [
                    'status' => false,
                    'message' => __('admin/order.messages.not_found'),
                ];
            }

            if (! $order->canTransitionTo($status)) {
                return [
                    'status' => false,
                    'message' => __('admin/order.messages.invalid_transition'),
                ];
            }

            DB::transaction(function () use ($order, $status, $reason) {
                $attributes = ['status' => $status];

                if ($status === OrderConst::STATUS_CONFIRMED) {
                    $attributes['confirmed_at'] = now();
                }

                if ($status === OrderConst::STATUS_COMPLETED) {
                    $attributes['completed_at'] = now();
                    $attributes['is_paid'] = true;
                }

                if ($status === OrderConst::STATUS_CANCELLED) {
                    $attributes['cancelled_at'] = now();
                    $attributes['cancel_reason'] = $reason;

                    $this->restoreStock($order);
                }

                $order->update($attributes);
            });

            return [
                'status' => true,
                'message' => __('admin/order.messages.status_updated'),
            ];
        } catch (\Throwable $th) {
            Log::error(__METHOD__, ['message' => $th->getMessage(), 'id' => $id, 'status' => $status]);

            throw $th;
        }
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

    public function markAsPaid(int|string $id): array
    {
        $order = $this->find($id);

        if (! $order) {
            return [
                'status' => false,
                'message' => __('admin/order.messages.not_found'),
            ];
        }

        if ($order->is_paid) {
            return [
                'status' => false,
                'message' => __('admin/order.messages.already_paid'),
            ];
        }

        $order->update(['is_paid' => true]);

        return [
            'status' => true,
            'message' => __('admin/order.messages.marked_paid'),
        ];
    }

    public function statistics(): array
    {
        $counts = Order::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'total' => (int) $counts->sum(),
            'pending' => (int) ($counts[OrderConst::STATUS_PENDING] ?? 0),
            'confirmed' => (int) ($counts[OrderConst::STATUS_CONFIRMED] ?? 0),
            'shipping' => (int) ($counts[OrderConst::STATUS_SHIPPING] ?? 0),
            'completed' => (int) ($counts[OrderConst::STATUS_COMPLETED] ?? 0),
            'cancelled' => (int) ($counts[OrderConst::STATUS_CANCELLED] ?? 0),
        ];
    }
}
