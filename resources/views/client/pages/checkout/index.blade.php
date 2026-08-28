@extends('client.layouts.app')

@section('title', 'Alibubu - Thanh toán')

@section('content')
    <nav class="flex items-center gap-2 text-sm text-muted-foreground mb-6">
        <a href="{{ route('index') }}" class="hover:text-primary transition-colors">Trang chủ</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <a href="{{ route('cart.index') }}" class="hover:text-primary transition-colors">Giỏ hàng</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-foreground font-medium">Thanh toán</span>
    </nav>

    <h1 class="text-2xl md:text-3xl font-bold text-foreground mb-6">Thanh toán</h1>

    <form action="{{ route('checkout.store') }}" method="POST" class="grid lg:grid-cols-3 gap-6 items-start">
        @csrf

        <div class="lg:col-span-2 bg-card border border-border rounded-xl p-5 md:p-6">
            <h2 class="font-bold text-foreground mb-5">Thông tin nhận hàng</h2>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="fullname" class="block text-sm font-medium text-foreground mb-1.5">
                        Họ và tên <span class="text-red-500">*</span>
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
                        Số điện thoại <span class="text-red-500">*</span>
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
                    <label for="email" class="block text-sm font-medium text-foreground mb-1.5">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', Auth::user()?->email) }}"
                        class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('email') ? 'is-invalid' : 'border-border' }}">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="address" class="block text-sm font-medium text-foreground mb-1.5">
                        Địa chỉ nhận hàng <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="address" name="address"
                        value="{{ old('address', $defaultAddress?->full_address) }}"
                        placeholder="Số nhà, đường, phường/xã, tỉnh/thành phố"
                        class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('address') ? 'is-invalid' : 'border-border' }}">
                    @error('address')
                        <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="note" class="block text-sm font-medium text-foreground mb-1.5">Ghi chú</label>
                    <textarea id="note" name="note" rows="3" placeholder="Ghi chú cho đơn hàng (không bắt buộc)"
                        class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('note') ? 'is-invalid' : 'border-border' }}">{{ old('note') }}</textarea>
                    @error('note')
                        <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="border-t border-border mt-6 pt-5">
                <h2 class="font-bold text-foreground mb-3">Phương thức thanh toán</h2>
                <div class="flex items-center gap-3 px-4 py-3 bg-primary/5 border border-primary/30 rounded-lg">
                    <i class="fa-solid fa-money-bill-wave text-primary"></i>
                    <div>
                        <p class="text-sm font-medium text-foreground">Thanh toán khi nhận hàng (COD)</p>
                        <p class="text-xs text-muted-foreground">Bạn thanh toán bằng tiền mặt khi nhận được hàng.</p>
                    </div>
                </div>
            </div>
        </div>

        <aside class="bg-card border border-border rounded-xl p-5 lg:sticky lg:top-24">
            <h2 class="font-bold text-foreground mb-4">Đơn hàng ({{ $items->sum('quantity') }})</h2>

            <div class="space-y-3 max-h-72 overflow-y-auto pr-1 mb-4">
                @foreach ($items as $item)
                    <div class="flex gap-3">
                        <span
                            class="relative w-14 h-14 shrink-0 bg-muted rounded-lg overflow-hidden flex items-center justify-center">
                            @if ($item['product']->thumbnail)
                                <img src="{{ Storage::disk('public')->url($item['product']->thumbnail) }}"
                                    alt="{{ $item['product']->name }}" class="w-full h-full object-cover">
                            @else
                                <i class="fa-solid fa-box-open text-muted-foreground/25"></i>
                            @endif
                            <span
                                class="absolute -top-1 -right-1 min-w-5 h-5 px-1 bg-primary text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                                {{ $item['quantity'] }}
                            </span>
                        </span>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-foreground line-clamp-2">{{ $item['product']->name }}</p>
                            @if ($item['variant'])
                                <p class="text-xs text-muted-foreground">
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

            <dl class="space-y-3 text-sm border-t border-border pt-4">
                <div class="flex justify-between">
                    <dt class="text-muted-foreground">Tạm tính</dt>
                    <dd class="font-medium text-foreground">{{ format_price($subtotal) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-muted-foreground">Phí vận chuyển</dt>
                    <dd class="font-medium text-green-600">Miễn phí</dd>
                </div>
            </dl>

            <div class="border-t border-border my-4"></div>

            <div class="flex justify-between items-baseline mb-5">
                <span class="font-semibold text-foreground">Tổng cộng</span>
                <span class="text-xl font-bold text-primary">{{ format_price($subtotal) }}</span>
            </div>

            <button type="submit"
                class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-primary rounded-xl hover:bg-primary/90 transition-colors">
                <i class="fa-solid fa-circle-check"></i>
                Đặt hàng
            </button>

            <a href="{{ route('cart.index') }}"
                class="w-full inline-flex items-center justify-center gap-2 mt-3 text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">
                <i class="fa-solid fa-arrow-left"></i> Quay lại giỏ hàng
            </a>
        </aside>
    </form>
@endsection
