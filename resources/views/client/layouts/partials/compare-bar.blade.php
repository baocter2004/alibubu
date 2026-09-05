@php $compareMax = \App\Services\Client\CompareService::MAX_ITEMS; @endphp

<div class="compare-bar {{ $compareItems->isEmpty() ? 'hidden' : '' }}" data-compare-bar
    data-compare-max="{{ $compareMax }}">
    <div class="max-w-7xl mx-auto px-4 py-3">
        <div class="flex items-center gap-3 md:gap-5">
            <div class="hidden sm:block shrink-0">
                <p class="text-xs font-bold uppercase tracking-wider text-muted-foreground">
                    {{ __('client.compare.bar_title') }}
                </p>
                <p class="text-sm font-semibold text-foreground tabular" data-compare-count>
                    {{ $compareItems->count() }}/{{ $compareMax }}
                </p>
            </div>

            <div class="flex-1 min-w-0 flex items-center gap-2 overflow-x-auto" data-compare-list>
                @foreach ($compareItems as $item)
                    <div class="relative shrink-0 w-14 h-14 bg-white border border-border rounded-xl overflow-hidden">
                        <a href="{{ route('shop.show', $item->slug) }}" title="{{ $item->name }}"
                            class="w-full h-full flex items-center justify-center">
                            @if ($item->thumbnail)
                                <img src="{{ Storage::disk('public')->url($item->thumbnail) }}" alt="{{ $item->name }}"
                                    class="w-full h-full object-contain p-1">
                            @else
                                <i class="fa-solid fa-box-open text-muted-foreground/30"></i>
                            @endif
                        </a>

                        <form action="{{ route('compare.destroy', $item->id) }}" method="POST"
                            class="absolute -top-1 -right-1" data-compare-remove data-product="{{ $item->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-5 h-5 rounded-full bg-foreground text-white text-[9px] flex items-center justify-center shadow hover:bg-danger transition-colors"
                                aria-label="{{ __('client.compare.remove') }}">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </form>
                    </div>
                @endforeach

                @for ($i = $compareItems->count(); $i < $compareMax; $i++)
                    <span
                        class="shrink-0 w-14 h-14 rounded-xl border border-dashed border-border flex items-center justify-center text-muted-foreground/40">
                        <i class="fa-solid fa-plus text-xs"></i>
                    </span>
                @endfor
            </div>

            <div class="flex items-center gap-1.5 shrink-0">
                <form action="{{ route('compare.clear') }}" method="POST" data-compare-remove>
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="hidden sm:inline-flex px-3 py-2 text-xs font-medium text-muted-foreground hover:text-danger transition-colors">
                        {{ __('client.compare.clear') }}
                    </button>
                </form>

                <a href="{{ route('compare.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold btn-primary rounded-xl whitespace-nowrap">
                    <i class="fa-solid fa-code-compare text-xs"></i>
                    <span class="hidden sm:inline">{{ __('client.compare.view') }}</span>
                    <span class="sm:hidden">{{ __('client.compare.add') }}</span>
                </a>

                <button type="button" data-compare-dismiss
                    class="w-9 h-9 flex items-center justify-center rounded-lg text-muted-foreground hover:bg-muted hover:text-foreground transition-colors"
                    aria-label="{{ __('client.compare.hide') }}" title="{{ __('client.compare.hide') }}">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<button type="button" data-compare-reopen
    class="hidden fixed right-4 bottom-20 md:bottom-6 z-40 items-center gap-2 pl-3 pr-4 py-2.5 text-sm font-bold btn-primary rounded-full shadow-lg">
    <i class="fa-solid fa-code-compare text-xs"></i>
    <span data-compare-count>{{ $compareItems->count() }}/{{ $compareMax }}</span>
</button>
