@extends('client.layouts.app')

@section('title', __('common.app_name') . ' - ' . __('client.wishlist.title'))

@section('content')
    <nav class="flex items-center gap-2 text-sm text-muted-foreground mb-6">
        <a href="{{ route('index') }}" class="hover:text-primary transition-colors">{{ __('client.nav.home') }}</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-foreground font-medium">{{ __('client.wishlist.title') }}</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-6 items-start">
        @include('client.pages.account.nav')

        <div class="flex-1 min-w-0">
            <section class="bg-card border border-border rounded-2xl p-5 md:p-6">
                <div class="mb-5">
                    <h1 class="text-lg font-bold text-foreground">{{ __('client.wishlist.title') }}</h1>
                    <p class="text-sm text-muted-foreground mt-0.5">{{ __('client.wishlist.subtitle') }}</p>
                </div>

                @if ($items->isEmpty())
                    <div class="py-16 text-center">
                        <i class="fa-regular fa-heart text-5xl text-muted-foreground/25 mb-4"></i>
                        <p class="text-foreground font-medium mb-4">{{ __('client.wishlist.empty') }}</p>
                        <a href="{{ route('shop.index') }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors">
                            <i class="fa-solid fa-bag-shopping"></i>
                            {{ __('client.cart.empty_cta') }}
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($items as $item)
                            @if ($item->product)
                                <div class="relative">
                                    @include('components.product-card', ['product' => $item->product])

                                    <form action="{{ route('account.wishlist.destroy', $item->id) }}" method="POST"
                                        class="absolute top-2 right-2 z-10">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-8 h-8 rounded-full bg-white/90 shadow flex items-center justify-center text-red-500 hover:bg-red-50 transition-colors"
                                            title="{{ __('client.wishlist.remove') }}">
                                            <i class="fa-solid fa-xmark text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    @include('components.pagination', ['paginator' => $items->withQueryString()])
                @endif
            </section>
        </div>
    </div>
@endsection
