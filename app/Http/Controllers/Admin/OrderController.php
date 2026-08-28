<?php

namespace App\Http\Controllers\Admin;

use App\Const\OrderConst;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Order\GetOrderRequest;
use App\Http\Requests\Admin\Order\UpdateOrderStatusRequest;
use App\Services\Admin\OrderService;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

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
            ->filter(['relates' => ['user', 'items.product', 'items.productVariant']])
            ->find($id);

        abort_if(! $order, 404);

        return view('admin.pages.orders.show', [
            'order' => $order,
            'transitions' => OrderConst::allowedTransitions($order->status),
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, int|string $id)
    {
        $data = $request->validated();

        $result = $this->orderService->updateStatus($id, (int) $data['status'], $data['cancel_reason'] ?? null);

        return redirect()
            ->route('admin.orders.show', $id)
            ->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    public function markPaid(int|string $id)
    {
        $result = $this->orderService->markAsPaid($id);

        return redirect()
            ->route('admin.orders.show', $id)
            ->with($result['status'] ? 'success' : 'error', $result['message']);
    }
}
