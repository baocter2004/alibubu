@extends('client.layouts.app')

@section('title', __('common.app_name') . ' - ' . __('client.tracking.title'))

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-8">
            <span class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-primary/10 text-primary mb-4">
                <i class="fa-solid fa-truck-fast text-xl"></i>
            </span>
            <h1 class="text-2xl md:text-3xl font-bold text-foreground mb-2">{{ __('client.tracking.heading') }}</h1>
            <p class="text-sm text-muted-foreground">{{ __('client.tracking.subheading') }}</p>
        </div>

        <section class="bg-card border border-border rounded-2xl shadow-sm p-5 md:p-8 mb-6">
            <form action="{{ route('order.track.lookup') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-4"
                data-submit-once>
                @csrf

                <div>
                    <label for="tracking-code" class="block text-sm font-medium text-foreground mb-1.5">
                        {{ __('client.tracking.fields.code') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="tracking-code" name="code" value="{{ old('code') }}"
                        placeholder="{{ __('client.tracking.placeholders.code') }}" autocomplete="off"
                        class="w-full px-4 py-2.5 text-sm uppercase border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('code') ? 'is-invalid' : 'border-border' }}">
                    @error('code')
                        <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tracking-phone" class="block text-sm font-medium text-foreground mb-1.5">
                        {{ __('client.tracking.fields.phone_number') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" id="tracking-phone" name="phone_number" value="{{ old('phone_number') }}"
                        placeholder="{{ __('client.tracking.placeholders.phone_number') }}" autocomplete="tel"
                        class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('phone_number') ? 'is-invalid' : 'border-border' }}">
                    @error('phone_number')
                        <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="sm:col-span-2 inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-bold btn-primary rounded-xl">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    {{ __('client.tracking.submit') }}
                </button>
            </form>
        </section>

        @isset($order)
            @php
                $steps = [
                    ['status' => \App\Const\OrderConst::STATUS_PENDING, 'icon' => 'fa-clock'],
                    ['status' => \App\Const\OrderConst::STATUS_CONFIRMED, 'icon' => 'fa-clipboard-check'],
                    ['status' => \App\Const\OrderConst::STATUS_SHIPPING, 'icon' => 'fa-truck-fast'],
                    ['status' => \App\Const\OrderConst::STATUS_COMPLETED, 'icon' => 'fa-circle-check'],
                ];
                $isCancelled = $order->status === \App\Const\OrderConst::STATUS_CANCELLED;
            @endphp

            <section class="bg-card border border-border rounded-2xl p-5 md:p-8" aria-labelledby="tracking-result-title">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-muted-foreground">{{ __('client.tracking.result_label') }}</p>
                        <h2 id="tracking-result-title" class="text-xl font-bold text-foreground">{{ $order->code }}</h2>
                    </div>
                    <span class="self-start px-3 py-1.5 text-xs font-semibold rounded-full {{ \App\Const\OrderConst::statusBadgeClass($order->status) }}">
                        {{ \App\Const\OrderConst::statusLabel($order->status) }}
                    </span>
                </div>

                @if ($isCancelled)
                    <div class="p-4 rounded-xl bg-red-50 border border-red-100 text-red-700" role="status">
                        <i class="fa-solid fa-ban mr-2"></i>{{ __('client.tracking.cancelled') }}
                    </div>
                @else
                    <div class="relative grid grid-cols-4 gap-2 mb-8" role="list" aria-label="{{ __('client.tracking.timeline') }}">
                        <div class="absolute left-[12.5%] right-[12.5%] top-5 h-0.5 bg-border" aria-hidden="true"></div>
                        @foreach ($steps as $step)
                            @php $done = $order->status >= $step['status']; @endphp
                            <div class="relative z-10 flex flex-col items-center text-center" role="listitem">
                                <span class="w-10 h-10 rounded-full flex items-center justify-center border-4 border-card {{ $done ? 'bg-primary text-white' : 'bg-muted text-muted-foreground' }}">
                                    <i class="fa-solid {{ $step['icon'] }} text-sm" aria-hidden="true"></i>
                                </span>
                                <span class="mt-2 text-[11px] sm:text-xs font-semibold leading-tight {{ $done ? 'text-foreground' : 'text-muted-foreground' }}">
                                    {{ \App\Const\OrderConst::statusLabel($step['status']) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="space-y-3 border-t border-border pt-5">
                    @foreach ($order->items as $item)
                        <div class="flex items-center gap-3">
                            <span class="w-12 h-12 shrink-0 rounded-lg bg-white border border-border overflow-hidden flex items-center justify-center">
                                @if ($item->product?->thumbnail)
                                    <img src="{{ Storage::disk('public')->url($item->product->thumbnail) }}" alt="{{ $item->name }}"
                                        class="w-full h-full object-contain p-1">
                                @else
                                    <i class="fa-solid fa-box-open text-muted-foreground/30" aria-hidden="true"></i>
                                @endif
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-medium text-foreground line-clamp-2">{{ $item->name }}</span>
                                <span class="block text-xs text-muted-foreground">× {{ $item->quantity }}</span>
                            </span>
                            <span class="text-sm font-semibold text-foreground whitespace-nowrap">
                                {{ format_price($item->price * $item->quantity) }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-between items-baseline border-t border-border mt-5 pt-5">
                    <span class="font-semibold text-foreground">{{ __('client.tracking.total') }}</span>
                    <span class="text-xl price-main">{{ format_price($order->total_amount) }}</span>
                </div>
            </section>
        @endisset
    </div>
@endsection
