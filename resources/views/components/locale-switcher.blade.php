@props(['align' => 'right'])

<div class="relative" data-locale-switcher>
    <button type="button" data-locale-toggle
        class="flex items-center gap-1.5 px-2.5 py-2 rounded-lg text-sm font-medium text-muted-foreground hover:text-foreground hover:bg-muted transition-colors"
        aria-haspopup="true" aria-expanded="false" aria-label="{{ __('common.locale.switch') }}">
        <i class="fa-solid fa-globe"></i>
        <span class="uppercase">{{ app()->getLocale() }}</span>
        <i class="fa-solid fa-chevron-down text-[10px]"></i>
    </button>

    <div data-locale-menu
        class="hidden absolute {{ $align === 'right' ? 'right-0' : 'left-0' }} top-full mt-1 min-w-36 bg-white border border-border rounded-xl shadow-lg overflow-hidden z-50">
        @foreach (config('app.supported_locales') as $locale)
            <a href="{{ route('locale.switch', $locale) }}"
                class="flex items-center justify-between gap-3 px-4 py-2.5 text-sm transition-colors {{ app()->getLocale() === $locale ? 'bg-primary/10 text-primary font-medium' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                {{ __('common.locale.' . $locale) }}
                @if (app()->getLocale() === $locale)
                    <i class="fa-solid fa-check text-xs"></i>
                @endif
            </a>
        @endforeach
    </div>
</div>
