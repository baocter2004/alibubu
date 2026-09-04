@extends('client.layouts.app')

@section('title', __('common.app_name') . ' - ' . $order->code)

@section('content')
    <nav class="flex flex-wrap items-center gap-2 text-sm text-muted-foreground mb-6">
        <a href="{{ route('index') }}" class="hover:text-primary transition-colors">{{ __('client.nav.home') }}</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <a href="{{ route('account.orders') }}"
            class="hover:text-primary transition-colors">{{ __('client.account.nav.orders') }}</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-foreground font-medium">{{ $order->code }}</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-6 items-start">
        @include('client.pages.account.nav')

        <div class="flex-1 min-w-0 space-y-6">
            <section class="bg-card border border-border rounded-2xl p-5 md:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                    <div>
                        <h1 class="text-lg font-bold text-foreground">{{ $order->code }}</h1>
                        <p class="text-sm text-muted-foreground mt-0.5">
                            {{ __('client.account.orders.placed_at') }}: {{ $order->created_at?->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <span
                            class="px-3 py-1.5 text-xs font-semibold rounded-full {{ \App\Const\OrderConst::statusBadgeClass($order->status) }}">
                            {{ \App\Const\OrderConst::statusLabel($order->status) }}
                        </span>
                        <span
                            class="px-3 py-1.5 text-xs font-semibold rounded-full {{ $order->is_paid ? 'bg-green-100 text-success' : 'bg-amber-100 text-amber-600' }}">
                            {{ $order->is_paid ? __('admin/order.payment.paid') : __('admin/order.payment.unpaid') }}
                        </span>
                    </div>
                </div>

                <div class="space-y-4">
                    @foreach ($order->items as $item)
                        <div class="flex gap-4 pb-4 border-b border-border last:border-0 last:pb-0">
                            <span
                                class="w-16 h-16 shrink-0 bg-muted rounded-lg overflow-hidden flex items-center justify-center">
                                @if ($item->product?->thumbnail)
                                    <img src="{{ Storage::disk('public')->url($item->product->thumbnail) }}"
                                        alt="{{ $item->name }}" class="w-full h-full object-cover">
                                @else
                                    <i class="fa-solid fa-box-open text-muted-foreground/30"></i>
                                @endif
                            </span>

                            <div class="flex-1 min-w-0">
                                @if ($item->product)
                                    <a href="{{ route('shop.show', $item->product->slug) }}"
                                        class="font-medium text-foreground hover:text-primary transition-colors line-clamp-2">
                                        {{ $item->name }}
                                    </a>
                                @else
                                    <p class="font-medium text-foreground line-clamp-2">{{ $item->name }}</p>
                                @endif

                                @if ($item->attributes_variant)
                                    <p class="text-xs text-muted-foreground mt-0.5">
                                        {{ implode(' / ', (array) $item->attributes_variant) }}
                                    </p>
                                @endif

                                <p class="text-sm text-muted-foreground mt-1">
                                    {{ format_price($item->price) }} × {{ $item->quantity }}
                                </p>
                            </div>

                            <span class="font-semibold text-foreground whitespace-nowrap">
                                {{ format_price($item->price * $item->quantity) }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="flex items-baseline justify-between pt-5 mt-5 border-t border-border">
                    <span class="font-semibold text-foreground">{{ __('client.cart.total') }}</span>
                    <span class="text-xl font-bold text-primary">{{ format_price($order->total_amount) }}</span>
                </div>
            </section>

            <section class="bg-card border border-border rounded-2xl p-5 md:p-6">
                <h2 class="font-bold text-foreground mb-4">{{ __('client.checkout.shipping_info') }}</h2>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    @php
                        $rows = [
                            ['label' => __('client.checkout.fullname'), 'value' => $order->fullname],
                            ['label' => __('client.checkout.phone_number'), 'value' => $order->phone_number],
                            ['label' => __('client.checkout.email'), 'value' => $order->email ?: '-'],
                        ];
                    @endphp

                    @foreach ($rows as $row)
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-muted-foreground mb-1">{{ $row['label'] }}</dt>
                            <dd class="text-foreground font-medium">{{ $row['value'] }}</dd>
                        </div>
                    @endforeach

                    <div class="sm:col-span-2">
                        <dt class="text-xs uppercase tracking-wide text-muted-foreground mb-1">
                            {{ __('client.checkout.address') }}</dt>
                        <dd class="text-foreground font-medium">{{ $order->address }}</dd>
                    </div>

                    @if ($order->note)
                        <div class="sm:col-span-2">
                            <dt class="text-xs uppercase tracking-wide text-muted-foreground mb-1">
                                {{ __('client.checkout.note') }}</dt>
                            <dd class="text-foreground">{{ $order->note }}</dd>
                        </div>
                    @endif
                </dl>

                @if ($order->cancel_reason)
                    <div class="mt-4 p-3 bg-red-50 border border-red-100 rounded-lg">
                        <p class="text-xs uppercase tracking-wide text-red-500 mb-1">
                            {{ __('admin/order.fields.cancel_reason') }}</p>
                        <p class="text-sm text-red-700">{{ $order->cancel_reason }}</p>
                    </div>
                @endif

                @if ($order->status === \App\Const\OrderConst::STATUS_CANCELLED)
                    <p class="mt-4 text-xs text-muted-foreground">
                        <i class="fa-solid fa-rotate-left mr-1"></i>{{ __('client.messages.stock_restored') }}
                    </p>
                @endif
            </section>

            @if (\App\Const\OrderConst::isCancellableByCustomer($order->status))
                <section class="bg-card border border-border rounded-2xl p-5 md:p-6">
                    <h2 class="text-base font-bold text-foreground mb-1">
                        {{ __('client.account.orders.cancel_title') }}
                    </h2>
                    <p class="text-sm text-muted-foreground mb-4">{{ __('client.account.orders.cancel_hint') }}</p>

                    <button type="button" id="toggle-cancel-form"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-red-600 border border-red-200 rounded-xl hover:bg-red-50 transition-colors">
                        <i class="fa-solid fa-ban"></i>
                        {{ __('client.account.orders.cancel') }}
                    </button>

                    <form id="cancel-order-form" action="{{ route('account.orders.cancel', $order->id) }}" method="POST"
                        class="{{ $errors->has('cancel_reason') ? '' : 'hidden' }} mt-4 space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="cancel_reason" class="block text-sm font-medium text-foreground mb-1.5">
                                {{ __('client.account.orders.cancel_reason') }}
                                <span class="text-muted-foreground font-normal">({{ __('common.labels.optional') }})</span>
                            </label>
                            <textarea id="cancel_reason" name="cancel_reason" rows="3"
                                placeholder="{{ __('client.account.orders.cancel_reason_placeholder') }}"
                                class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('cancel_reason') ? 'is-invalid' : 'border-border' }}">{{ old('cancel_reason') }}</textarea>
                            @error('cancel_reason')
                                <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-wrap justify-end gap-3">
                            <button type="button" id="keep-order"
                                class="px-5 py-2.5 text-sm font-medium text-muted-foreground border border-border rounded-lg hover:bg-muted transition-colors">
                                {{ __('client.account.orders.cancel_keep') }}
                            </button>
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                                <i class="fa-solid fa-ban"></i>
                                {{ __('client.account.orders.cancel_submit') }}
                            </button>
                        </div>
                    </form>
                </section>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#toggle-cancel-form').on('click', function() {
                $('#cancel-order-form').removeClass('hidden');
                $('#cancel_reason').trigger('focus');
            });

            $('#keep-order').on('click', function() {
                $('#cancel-order-form').addClass('hidden');
            });
        });
    </script>
@endpush
