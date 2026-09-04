@extends('client.layouts.app')

@section('title', __('common.app_name') . ' - ' . __('client.checkout.title'))

@section('content')
    <nav class="flex items-center gap-2 text-sm text-muted-foreground mb-6">
        <a href="{{ route('index') }}" class="hover:text-primary transition-colors">{{ __('client.nav.home') }}</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <a href="{{ route('cart.index') }}" class="hover:text-primary transition-colors">{{ __('client.cart.breadcrumb') }}</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-foreground font-medium">{{ __('client.checkout.title') }}</span>
    </nav>

    <div class="flex items-center justify-center gap-2 sm:gap-4 mb-8">
        @php
            $steps = [
                ['key' => 'cart', 'icon' => 'fa-cart-shopping', 'state' => 'done'],
                ['key' => 'info', 'icon' => 'fa-truck', 'state' => 'current'],
                ['key' => 'done', 'icon' => 'fa-circle-check', 'state' => 'next'],
            ];
        @endphp

        @foreach ($steps as $index => $step)
            <div class="flex items-center gap-2">
                <span
                    class="w-9 h-9 rounded-full flex items-center justify-center text-sm transition-colors {{ $step['state'] === 'next' ? 'bg-muted text-muted-foreground' : 'bg-primary text-white' }}">
                    <i class="fa-solid {{ $step['icon'] }} text-xs"></i>
                </span>
                <span
                    class="hidden sm:block text-sm font-medium {{ $step['state'] === 'next' ? 'text-muted-foreground' : 'text-foreground' }}">
                    {{ __('client.checkout.steps.' . $step['key']) }}
                </span>
            </div>

            @if ($index < count($steps) - 1)
                <span class="w-8 sm:w-16 h-0.5 {{ $step['state'] === 'done' ? 'bg-primary' : 'bg-border' }}"></span>
            @endif
        @endforeach
    </div>

    <form action="{{ route('checkout.store') }}" method="POST" class="grid lg:grid-cols-3 gap-6 items-start">
        @csrf

        <div class="lg:col-span-2 space-y-6">
            <section class="bg-card border border-border rounded-2xl p-5 md:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                    <h2 class="font-bold text-foreground">{{ __('client.checkout.shipping_info') }}</h2>

                    @if ($defaultAddress)
                        <button type="button" id="use-saved-address"
                            class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors"
                            data-fullname="{{ $defaultAddress->fullname }}"
                            data-phone="{{ $defaultAddress->phone_number }}"
                            data-address="{{ $defaultAddress->full_address }}">
                            <i class="fa-solid fa-location-dot"></i>
                            {{ __('client.checkout.use_saved_address') }}
                        </button>
                    @endif
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="fullname" class="block text-sm font-medium text-foreground mb-1.5">
                            {{ __('client.checkout.fullname') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="fullname" name="fullname"
                            value="{{ old('fullname', $defaultAddress->fullname ?? Auth::user()?->fullname) }}"
                            class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('fullname') ? 'is-invalid' : 'border-border' }}">
                        @error('fullname')
                            <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-foreground mb-1.5">
                            {{ __('client.checkout.phone_number') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" id="phone_number" name="phone_number"
                            value="{{ old('phone_number', $defaultAddress->phone_number ?? Auth::user()?->phone_number) }}"
                            placeholder="0901234567"
                            class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('phone_number') ? 'is-invalid' : 'border-border' }}">
                        @error('phone_number')
                            <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="email" class="block text-sm font-medium text-foreground mb-1.5">
                            {{ __('client.checkout.email') }}
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', Auth::user()?->email) }}"
                            class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('email') ? 'is-invalid' : 'border-border' }}">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="address" class="block text-sm font-medium text-foreground mb-1.5">
                            {{ __('client.checkout.address') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="address" name="address"
                            value="{{ old('address', $defaultAddress?->full_address) }}"
                            placeholder="{{ __('client.checkout.address_placeholder') }}"
                            class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('address') ? 'is-invalid' : 'border-border' }}">
                        @error('address')
                            <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="note" class="block text-sm font-medium text-foreground mb-1.5">
                            {{ __('client.checkout.note') }}
                        </label>
                        <textarea id="note" name="note" rows="3" placeholder="{{ __('client.checkout.note_placeholder') }}"
                            class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('note') ? 'is-invalid' : 'border-border' }}">{{ old('note') }}</textarea>
                        @error('note')
                            <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <section class="bg-card border border-border rounded-2xl p-5 md:p-6">
                <h2 class="font-bold text-foreground mb-4">{{ __('client.checkout.payment_method') }}</h2>

                <div class="space-y-3">
                    @foreach (\App\Const\PaymentConst::methods() as $value => $label)
                        <label class="block cursor-pointer">
                            <input type="radio" name="payment_method" value="{{ $value }}"
                                @checked((int) old('payment_method', \App\Const\PaymentConst::METHOD_COD) === (int) $value)
                                class="peer sr-only payment-option">
                            <span
                                class="flex items-start gap-3 px-4 py-3.5 border-2 border-border rounded-xl transition-all peer-checked:border-primary peer-checked:bg-primary/5">
                                <span class="w-10 h-10 shrink-0 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                                    <i class="fa-solid {{ \App\Const\PaymentConst::methodIcon((int) $value) }}"></i>
                                </span>
                                <span class="flex-1">
                                    <span class="block text-sm font-semibold text-foreground">{{ $label }}</span>
                                    <span class="block text-xs text-muted-foreground mt-0.5">
                                        {{ (int) $value === \App\Const\PaymentConst::METHOD_BANK_TRANSFER ? __('client.checkout.method_bank_desc') : __('client.checkout.method_cod_desc') }}
                                    </span>
                                </span>
                                <i class="fa-solid fa-circle-check text-primary opacity-0 peer-checked:opacity-100 mt-2.5"></i>
                            </span>
                        </label>
                    @endforeach
                </div>

                @error('payment_method')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror

                <div id="bank-details"
                    class="hidden mt-4 p-4 bg-muted/50 border border-border rounded-xl text-sm space-y-2">
                    <p class="font-semibold text-foreground">{{ __('client.checkout.bank_details') }}</p>
                    <dl class="grid grid-cols-3 gap-2">
                        <dt class="text-muted-foreground">{{ __('client.checkout.bank_name') }}</dt>
                        <dd class="col-span-2 text-foreground font-medium">Vietcombank</dd>
                        <dt class="text-muted-foreground">{{ __('client.checkout.bank_account') }}</dt>
                        <dd class="col-span-2 text-foreground font-medium">0123 4567 8910</dd>
                        <dt class="text-muted-foreground">{{ __('client.checkout.bank_holder') }}</dt>
                        <dd class="col-span-2 text-foreground font-medium">CONG TY ALIBUBU</dd>
                    </dl>
                    <p class="text-xs text-muted-foreground pt-1">{{ __('client.checkout.bank_note_hint') }}</p>
                </div>
            </section>
        </div>

        <aside class="bg-card border border-border rounded-2xl p-5 lg:sticky lg:top-24">
            <h2 class="font-bold text-foreground mb-4">
                {{ __('client.checkout.summary', ['count' => $items->sum('quantity')]) }}
            </h2>

            <div class="space-y-3 max-h-72 overflow-y-auto pr-1 mb-4">
                @foreach ($items as $item)
                    <div class="flex gap-3">
                        <span
                            class="relative w-14 h-14 shrink-0 bg-white border border-border rounded-lg overflow-hidden flex items-center justify-center">
                            @if ($item['product']->thumbnail)
                                <img src="{{ Storage::disk('public')->url($item['product']->thumbnail) }}"
                                    alt="{{ $item['product']->name }}" class="w-full h-full object-contain p-1">
                            @else
                                <i class="fa-solid fa-box-open text-muted-foreground/25"></i>
                            @endif
                            <span
                                class="absolute -top-1.5 -right-1.5 min-w-5 h-5 px-1 bg-primary text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                                {{ $item['quantity'] }}
                            </span>
                        </span>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-foreground line-clamp-2 leading-snug">{{ $item['product']->name }}</p>
                            @if ($item['variant'])
                                <p class="text-xs text-muted-foreground mt-0.5">
                                    {{ $item['variant']->attributeValues->pluck('value')->implode(' / ') ?: $item['variant']->sku }}
                                </p>
                            @endif
                        </div>

                        <span class="text-sm font-medium text-foreground whitespace-nowrap">
                            {{ format_price($item['subtotal']) }}
                        </span>
                    </div>
                @endforeach
            </div>

            <dl class="space-y-3 text-sm border-t border-border pt-4 mb-4">
                <div class="flex justify-between">
                    <dt class="text-muted-foreground">{{ __('client.cart.subtotal') }}</dt>
                    <dd class="font-medium text-foreground">{{ format_price($subtotal) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-muted-foreground">{{ __('client.cart.shipping') }}</dt>
                    <dd class="font-medium text-success">{{ __('client.cart.free') }}</dd>
                </div>
            </dl>

            @if ($coupon)
                <div class="flex items-center justify-between gap-2 px-3 py-2 bg-success-soft border border-success/25 rounded-lg mb-4">
                    <span class="text-xs font-semibold text-success truncate">
                        {{ __('client.coupon.applied_label', ['code' => $coupon->code]) }}
                    </span>
                    <span class="text-sm font-medium text-success whitespace-nowrap">-{{ format_price($discount) }}</span>
                </div>
            @endif

            <div class="border-t border-border my-4"></div>

            <div class="flex justify-between items-baseline mb-5">
                <span class="font-semibold text-foreground">{{ __('client.cart.total') }}</span>
                <span class="text-2xl price-main">{{ format_price($total) }}</span>
            </div>

            <button type="submit"
                class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 text-sm font-bold btn-accent rounded-xl">
                <i class="fa-solid fa-circle-check"></i>
                {{ __('client.checkout.place_order') }}
            </button>

            <a href="{{ route('cart.index') }}"
                class="w-full inline-flex items-center justify-center gap-2 mt-3 text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">
                <i class="fa-solid fa-arrow-left"></i> {{ __('client.checkout.back_to_cart') }}
            </a>
        </aside>
    </form>
@endsection

@push('scripts')
    <script>
        $(function() {
            function toggleBank() {
                const isBank = $('.payment-option:checked').val() === '{{ \App\Const\PaymentConst::METHOD_BANK_TRANSFER }}';
                $('#bank-details').toggleClass('hidden', !isBank);
            }

            $('.payment-option').on('change', toggleBank);
            toggleBank();

            $('#use-saved-address').on('click', function() {
                $('#fullname').val($(this).data('fullname'));
                $('#phone_number').val($(this).data('phone'));
                $('#address').val($(this).data('address'));
            });
        });
    </script>
@endpush
