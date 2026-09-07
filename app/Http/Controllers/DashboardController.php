<?php

namespace App\Http\Controllers;

use App\Const\OrderConst;
use App\Const\PaymentConst;
use App\Const\UserConst;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $allowedRanges = [7, 30, 90, 365];
        $range = (int) $request->input('range', 30);
        $range = in_array($range, $allowedRanges, true) ? $range : 30;
        $lowStockThreshold = min(100, max(1, (int) $request->input('low_stock_threshold', 5)));
        $from = now()->startOfDay()->subDays($range - 1);
        $to = now()->endOfDay();
        $orders = Order::query()
            ->whereBetween('created_at', [$from, $to])
            ->get(['created_at', 'total_amount', 'is_paid', 'status', 'payment_method']);
        $labels = [];
        $revenue = [];
        $orderCounts = [];
        $cursor = $from->copy();

        while ($cursor->lte($to)) {
            $key = $cursor->toDateString();
            $labels[] = $cursor->format('d/m');
            $revenue[$key] = 0.0;
            $orderCounts[$key] = 0;
            $cursor->addDay();
        }

        $statusCounts = array_fill_keys(array_keys(OrderConst::statuses()), 0);
        $paymentCounts = array_fill_keys(array_keys(PaymentConst::methods()), 0);
        foreach ($orders as $order) {
            $key = Carbon::parse($order->created_at)->toDateString();
            $orderCounts[$key] = ($orderCounts[$key] ?? 0) + 1;
            if ($order->is_paid && $order->status !== OrderConst::STATUS_CANCELLED) {
                $revenue[$key] = ($revenue[$key] ?? 0) + (float) $order->total_amount;
            }
            $statusCounts[$order->status] = ($statusCounts[$order->status] ?? 0) + 1;
            $paymentCounts[$order->payment_method] = ($paymentCounts[$order->payment_method] ?? 0) + 1;
        }

        $revenueValues = array_values($revenue);
        $orderValues = array_values($orderCounts);
        $cumulativeRevenue = [];
        $runningRevenue = 0.0;
        foreach ($revenueValues as $value) {
            $runningRevenue += (float) $value;
            $cumulativeRevenue[] = $runningRevenue;
        }
        $paidOrders = $orders->filter(fn ($order) => $order->is_paid && $order->status !== OrderConst::STATUS_CANCELLED);
        $periodRevenue = (float) array_sum($revenueValues);
        $periodOrders = $orders->count();
        $labelStep = $range <= 30 ? 5 : ($range <= 90 ? 14 : 30);
        $topProducts = OrderItem::query()
            ->select('name')
            ->selectRaw('SUM(quantity) as quantity')
            ->selectRaw('SUM(price * quantity) as revenue')
            ->whereHas('order', fn ($query) => $query
                ->where('is_paid', true)
                ->where('status', '!=', OrderConst::STATUS_CANCELLED)
                ->whereBetween('created_at', [$from, $to]))
            ->groupBy('name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();
        $inventory = Product::query()
            ->where('is_active', true)
            ->get(['stock', 'is_active'])
            ->groupBy(fn ($product) => $product->stock > 0 ? ($product->stock <= $lowStockThreshold ? 'low_stock' : 'in_stock') : 'out_of_stock')
            ->map->count()
            ->all();
        $inventory = array_merge(['in_stock' => 0, 'low_stock' => 0, 'out_of_stock' => 0], $inventory);
        $statusConfig = $this->axisConfig((float) max(array_values($statusCounts) ?: [0]), true);
        $inventoryConfig = $this->axisConfig((float) max(array_values($inventory) ?: [0]), true);
        $revenueConfig = $this->axisConfig(max($revenueValues ?: [0]));
        $cumulativeRevenueConfig = $this->axisConfig(max($cumulativeRevenue ?: [0]));
        $ordersConfig = $this->axisConfig(max($orderValues ?: [0]), true);
        $topProductsConfig = $this->axisConfig((float) ($topProducts->max('revenue') ?? 0));
        $paymentConfig = $this->axisConfig((float) max(array_values($paymentCounts) ?: [0]), true);

        return view('admin.pages.dashboard', [
            'stats' => [
                'users' => User::count(),
                'products' => Product::count(),
                'categories' => Category::count(),
                'branches' => Branch::count(),
                'orders' => Order::count(),
                'revenue' => (float) Order::where('is_paid', true)
                    ->where('status', '!=', OrderConst::STATUS_CANCELLED)
                    ->sum('total_amount'),
            ],
            'latestOrders' => Order::with('user')->latest('id')->limit(5)->get(),
            'latestUsers' => User::where('role', UserConst::ROLE_USER)->latest('id')->limit(5)->get(),
            'selectedRange' => $range,
            'lowStockThreshold' => $lowStockThreshold,
            'period' => [
                'from' => $from->format('d/m/Y'),
                'to' => $to->format('d/m/Y'),
                'revenue' => $periodRevenue,
                'orders' => $periodOrders,
                'paid_orders' => $paidOrders->count(),
                'average_order' => $paidOrders->count() > 0 ? $periodRevenue / $paidOrders->count() : 0,
            ],
            'chart' => [
                'labels' => $labels,
                'revenue' => $revenueValues,
                'cumulative_revenue' => $cumulativeRevenue,
                'orders' => $orderValues,
                'max_revenue' => max($revenueValues ?: [0]),
                'max_orders' => max($orderValues ?: [0]),
                'label_step' => $labelStep,
            ],
            'statusCounts' => $statusCounts,
            'paymentCounts' => $paymentCounts,
            'topProducts' => $topProducts,
            'inventory' => $inventory,
            'chartConfig' => [
                'revenue' => $revenueConfig,
                'cumulative_revenue' => $cumulativeRevenueConfig,
                'orders' => $ordersConfig,
                'top_products' => $topProductsConfig,
                'status' => $statusConfig,
                'inventory' => array_merge($inventoryConfig, ['low_stock_threshold' => $lowStockThreshold]),
                'payment' => $paymentConfig,
            ],
        ]);
    }

    protected function axisConfig(float $value, bool $integer = false): array
    {
        if ($value <= 0) {
            return ['min' => 0, 'max' => 4, 'step' => 1];
        }

        $segments = 5;
        $rawStep = $value / $segments;
        $power = 10 ** floor(log10($rawStep));
        $normalized = $rawStep / $power;
        $niceNormalized = $normalized <= 1 ? 1 : ($normalized <= 2 ? 2 : ($normalized <= 2.5 ? 2.5 : ($normalized <= 5 ? 5 : 10)));
        $step = $niceNormalized * $power;

        if ($integer) {
            $step = max(1, ceil($step));
        }

        $max = ceil($value / $step) * $step;
        $max = $integer ? max(4, $max) : $max;

        return [
            'min' => 0,
            'max' => $max,
            'step' => $step,
        ];
    }
}
