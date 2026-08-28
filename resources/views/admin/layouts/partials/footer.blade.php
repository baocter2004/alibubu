<footer class="bg-foreground text-white mt-4 md:mt-16">
    <div class="border-t border-white/10">
        <div class="max-w-7xl mx-auto px-6 py-5 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-white/40 text-center sm:text-left">
                {{ __('client.footer.copyright', ['year' => now()->year]) }}
            </p>
            <div class="flex items-center gap-4">
                @foreach (['terms', 'privacy', 'cookie'] as $link)
                    <a href="#" class="text-xs text-white/40 hover:text-white/70 transition-colors">
                        {{ __('client.footer.' . $link) }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</footer>
