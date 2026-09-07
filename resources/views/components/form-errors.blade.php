@if ($errors->any())
    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4" role="alert">
        <p class="flex items-center gap-2 text-sm font-semibold text-red-700 mb-2">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ __('common.validation.summary') }}
        </p>
        <ul class="list-disc list-inside space-y-1 text-sm text-red-600">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
