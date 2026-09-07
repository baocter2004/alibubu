@php
    $coupon = $coupon ?? null;
    $discount = $discount ?? 0;
    $availableCoupons = $availableCoupons ?? collect();
@endphp

<div class="border-t border-border pt-4 mb-4">
    <p class="text-sm font-semibold text-foreground mb-2.5">
        <i class="fa-solid fa-ticket text-primary mr-1.5"></i>{{ __('client.coupon.title') }}
    </p>

    @if ($coupon)
        <div class="flex items-center justify-between gap-3 px-3 py-2.5 bg-success-soft border border-success/25 rounded-xl">
            <span class="min-w-0">
                <span class="block text-sm font-semibold text-success truncate">
                    {{ __('client.coupon.applied_label', ['code' => $coupon->code]) }}
                </span>
                @if ($coupon->title)
                    <span class="block text-xs text-success truncate">{{ $coupon->title }}</span>
                @endif
            </span>

            <form action="{{ route('coupon.destroy') }}" method="POST" data-submit-once>
                @csrf
                @method('DELETE')
                <button type="submit" class="text-xs font-medium text-success hover:text-red-600 transition-colors">
                    {{ __('client.coupon.remove') }}
                </button>
            </form>
        </div>
    @else
        <form action="{{ route('coupon.store') }}" method="POST" class="flex gap-2" data-submit-once>
            @csrf
            <input type="text" name="code" value="{{ old('code') }}"
                placeholder="{{ __('client.coupon.placeholder') }}"
                class="flex-1 min-w-0 px-3 py-2.5 text-sm border rounded-lg uppercase focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('code') ? 'is-invalid' : 'border-border' }}">
            <button type="submit"
                class="px-4 py-2.5 text-sm font-semibold text-primary border border-primary/40 rounded-lg hover:bg-primary hover:text-white transition-colors whitespace-nowrap">
                {{ __('client.coupon.apply') }}
            </button>
        </form>
        @error('code')
            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
        @enderror

        @if ($availableCoupons->isNotEmpty())
            <p class="text-xs text-muted-foreground mt-3 mb-2">{{ __('client.coupon.available') }}</p>
            <div class="space-y-2">
                @foreach ($availableCoupons as $available)
                    <form action="{{ route('coupon.store') }}" method="POST" data-submit-once>
                        @csrf
                        <input type="hidden" name="code" value="{{ $available->code }}">
                        <button type="submit"
                            class="w-full flex items-center gap-3 px-3 py-2.5 text-left border border-dashed border-primary/35 rounded-xl hover:border-primary hover:bg-primary-soft/50 transition-colors">
                            <span class="w-9 h-9 shrink-0 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                                <i class="fa-solid fa-ticket text-xs"></i>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-semibold text-foreground truncate">{{ $available->code }}</span>
                                @if ($available->title)
                                    <span class="block text-xs text-muted-foreground truncate">{{ $available->title }}</span>
                                @endif
                                @if ($available->end_date)
                                    <span class="block text-[11px] text-muted-foreground/80">
                                        {{ __('client.coupon.expires', ['date' => $available->end_date->format('d/m/Y')]) }}
                                    </span>
                                @endif
                            </span>
                            <span class="text-xs font-bold text-primary shrink-0">{{ __('client.coupon.use') }}</span>
                        </button>
                    </form>
                @endforeach
            </div>
        @endif
    @endif
</div>
