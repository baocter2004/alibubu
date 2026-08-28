<footer class="border-t border-gray-200 bg-white">
    <div class="max-w-[1600px] mx-auto px-4 md:px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-2">
        <p class="text-xs text-gray-500 text-center sm:text-left">
            {{ __('client.footer.copyright', ['year' => now()->year]) }}
        </p>
        <div class="flex items-center gap-4">
            @foreach (['terms', 'privacy', 'cookie'] as $link)
                <a href="#" class="text-xs text-gray-400 hover:text-gray-600 transition-colors">
                    {{ __('client.footer.' . $link) }}
                </a>
            @endforeach
        </div>
    </div>
</footer>
