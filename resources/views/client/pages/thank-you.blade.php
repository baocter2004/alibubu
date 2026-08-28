@extends('client.layouts.app')

@section('title', __('common.app_name') . ' - ' . __('client.thank_you.title'))

@section('content')
    <div class="max-w-lg mx-auto text-center py-14">
        <span class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 text-green-600 mb-6">
            <i class="fa-solid fa-check text-3xl"></i>
        </span>

        <h1 class="text-2xl md:text-3xl font-bold text-foreground mb-3">{{ __('client.thank_you.title') }}</h1>
        <p class="text-muted-foreground mb-6">{{ __('client.thank_you.description') }}</p>

        @if ($orderCode)
            <div class="inline-flex flex-col items-center gap-1 px-6 py-4 bg-card border border-border rounded-xl mb-7">
                <span class="text-xs text-muted-foreground uppercase tracking-wide">{{ __('client.thank_you.order_code') }}</span>
                <span class="text-lg font-bold text-primary tracking-wider">{{ $orderCode }}</span>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('shop.index') }}"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-primary rounded-xl hover:bg-primary/90 transition-colors">
                <i class="fa-solid fa-bag-shopping"></i> {{ __('client.thank_you.continue') }}
            </a>
            <a href="{{ route('index') }}"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-foreground bg-white border border-border rounded-xl hover:bg-muted transition-colors">
                <i class="fa-solid fa-house"></i> {{ __('client.thank_you.home') }}
            </a>
        </div>
    </div>
@endsection
