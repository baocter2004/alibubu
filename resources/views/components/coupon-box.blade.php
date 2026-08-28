@php
    $coupon = $coupon ?? null;
    $discount = $discount ?? 0;
@endphp

<div class="border-t border-border pt-4 mb-4">
    <p class="text-sm font-semibold text-foreground mb-2.5">
        <i class="fa-solid fa-ticket text-primary mr-1.5"></i>{{ __('client.coupon.title') }}
    </p>

    @if ($coupon)
        <div class="flex items-center justify-between gap-3 px-3 py-2.5 bg-green-50 border border-green-200 rounded-xl">
            <span class="min-w-0">
                <span class="block text-sm font-semibold text-green-700 truncate">
                    {{ __('client.coupon.applied_label', ['code' => $coupon->code]) }}
                </span>
                @if ($coupon->title)
                    <span class="block text-xs text-green-600 truncate">{{ $coupon->title }}</span>
                @endif
            </span>

            <form action="{{ route('coupon.destroy') }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-xs font-medium text-green-700 hover:text-red-600 transition-colors">
                    {{ __('client.coupon.remove') }}
                </button>
            </form>
        </div>
    @else
        <form action="{{ route('coupon.store') }}" method="POST" class="flex gap-2">
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
    @endif
</div>
