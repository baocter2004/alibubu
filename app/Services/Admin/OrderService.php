<?php

namespace App\Services\Admin;

use App\Const\OrderConst;
use App\Exceptions\OrderStateException;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\BaseCrudService;
use App\Services\Order\OrderStateService;
use Illuminate\Support\Arr;

class OrderService extends BaseCrudService
{
    public function __construct(protected OrderStateService $orderState)
    {
        parent::__construct();
    }

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

    public function updateStatus(int|string $id, int $status, string $adminId, ?string $reason = null): array
    {
        $order = $this->find($id);

        if (! $order) {
            return [
                'status' => false,
                'message' => __('admin/order.messages.not_found'),
            ];
        }

        try {
            $this->orderState->transition($order, $status, OrderConst::ACTOR_ADMIN, $adminId, $reason);
        } catch (OrderStateException $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }

        return [
            'status' => true,
            'message' => __('admin/order.messages.status_updated'),
        ];
    }

    public function markAsPaid(int|string $id, string $adminId, ?string $reference = null, ?string $note = null): array
    {
        try {
            $this->orderState->markPaidManually($id, $adminId, $reference, $note);
        } catch (OrderStateException $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }

        return [
            'status' => true,
            'message' => __('admin/order.messages.marked_paid'),
        ];
    }

    public function markRefunded(int|string $id, string $adminId, string $note, ?string $reference = null): array
    {
        try {
            $this->orderState->markRefunded($id, $adminId, $note, $reference);
        } catch (OrderStateException $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }

        return [
            'status' => true,
            'message' => __('admin/order.messages.marked_refunded'),
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
