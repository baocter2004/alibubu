@extends('client.layouts.app')

@section('title', __('common.app_name') . ' - ' . __('client.account.nav.orders'))

@section('content')
    <nav class="flex items-center gap-2 text-sm text-muted-foreground mb-6">
        <a href="{{ route('index') }}" class="hover:text-primary transition-colors">{{ __('client.nav.home') }}</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-foreground font-medium">{{ __('client.account.nav.orders') }}</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-6 items-start">
        @include('client.pages.account.nav')

        <div class="flex-1 min-w-0">
            <section class="bg-card border border-border rounded-2xl p-5 md:p-6">
                <div class="mb-5">
                    <h1 class="text-lg font-bold text-foreground">{{ __('client.account.orders.title') }}</h1>
                    <p class="text-sm text-muted-foreground mt-0.5">{{ __('client.account.orders.subtitle') }}</p>
                </div>

                <form action="{{ route('account.orders') }}" method="GET"
                    class="flex flex-col sm:flex-row gap-3 mb-6">
                    <div class="relative flex-1">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground"></i>
                        <input type="search" name="keyword" value="{{ request('keyword') }}"
                            placeholder="{{ __('client.account.orders.search') }}"
                            class="w-full pl-9 pr-4 py-2.5 text-sm border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    </div>

                    <select name="status" onchange="this.form.submit()"
                        class="px-4 py-2.5 text-sm border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        <option value="">{{ __('common.labels.all') }}</option>
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}" @selected((string) request('status') === (string) $key)>{{ $label }}</option>
                        @endforeach
                    </select>

                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors">
                        {{ __('common.actions.search') }}
                    </button>
                </form>

                @if ($orders->isEmpty())
                    <div class="py-16 text-center">
                        <i class="fa-solid fa-receipt text-5xl text-muted-foreground/25 mb-4"></i>
                        <p class="text-foreground font-medium mb-4">{{ __('client.account.orders.empty') }}</p>
                        <a href="{{ route('shop.index') }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors">
                            <i class="fa-solid fa-bag-shopping"></i>
                            {{ __('client.account.orders.empty_cta') }}
                        </a>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($orders as $order)
                            <a href="{{ route('account.orders.show', $order->id) }}"
                                class="flex flex-col sm:flex-row sm:items-center gap-3 p-4 border border-border rounded-xl hover:border-primary/50 hover:bg-muted/40 transition-all">
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-foreground">{{ $order->code }}</p>
                                    <p class="text-xs text-muted-foreground mt-0.5">
                                        {{ __('client.account.orders.placed_at') }}:
                                        {{ $order->created_at?->format('d/m/Y H:i') }}
                                        · {{ $order->items_count }} {{ __('client.account.orders.items') }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-3">
                                    <span
                                        class="px-2.5 py-1 text-xs font-semibold rounded-full {{ \App\Const\OrderConst::statusBadgeClass($order->status) }}">
                                        {{ \App\Const\OrderConst::statusLabel($order->status) }}
                                    </span>
                                    <span class="font-bold text-primary whitespace-nowrap">
                                        {{ format_price($order->total_amount) }}
                                    </span>
                                    <i class="fa-solid fa-chevron-right text-xs text-muted-foreground"></i>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    @include('components.pagination', ['paginator' => $orders->withQueryString()])
                @endif
            </section>
        </div>
    </div>
@endsection
