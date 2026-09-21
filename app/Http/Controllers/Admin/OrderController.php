<?php

namespace App\Http\Controllers\Admin;

use App\Const\OrderConst;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Order\GetOrderRequest;
use App\Http\Requests\Admin\Order\MarkPaidRequest;
use App\Http\Requests\Admin\Order\MarkRefundedRequest;
use App\Http\Requests\Admin\Order\UpdateOrderStatusRequest;
use App\Services\Admin\OrderService;
use App\Services\Order\OrderStateService;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected OrderStateService $orderState
    ) {}

    public function index(GetOrderRequest $request)
    {
        return view('admin.pages.orders.index', [
            'orders' => $this->orderService->search(
                array_merge($request->validated(), ['relates' => ['user'], 'relates_count' => ['items']])
            ),
            'statuses' => OrderConst::statuses(),
            'statistics' => $this->orderService->statistics(),
        ]);
    }

    public function show(int|string $id)
    {
        $order = $this->orderService
            ->filter(['relates' => ['user', 'items.product', 'items.productVariant', 'histories', 'paymentTransactions']])
            ->find($id);

        abort_if(! $order, 404);

        return view('admin.pages.orders.show', [
            'order' => $order,
            'transitions' => $this->orderState->availableTransitions($order, OrderConst::ACTOR_ADMIN),
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, int|string $id)
    {
        $data = $request->validated();

        $result = $this->orderService->updateStatus(
            $id,
            (int) $data['status'],
            (string) Auth::guard('admin')->id(),
            $data['cancel_reason'] ?? null
        );

        return redirect()
            ->route('admin.orders.show', $id)
            ->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    public function markPaid(MarkPaidRequest $request, int|string $id)
    {
        $data = $request->validated();

        $result = $this->orderService->markAsPaid(
            $id,
            (string) Auth::guard('admin')->id(),
            $data['reference'] ?? null,
            $data['note'] ?? null
        );

        return redirect()
            ->route('admin.orders.show', $id)
            ->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    public function markRefunded(MarkRefundedRequest $request, int|string $id)
    {
        $data = $request->validated();

        $result = $this->orderService->markRefunded(
            $id,
            (string) Auth::guard('admin')->id(),
            $data['note'],
            $data['reference'] ?? null
        );

        return redirect()
            ->route('admin.orders.show', $id)
            ->with($result['status'] ? 'success' : 'error', $result['message']);
    }
}
