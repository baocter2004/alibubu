@extends('admin.layouts.app')

@section('title', __('admin/product.import.title'))

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ __('admin/product.import.title') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('admin/product.import.subtitle') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.products.import.template') }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-primary bg-primary-soft rounded-lg hover:bg-primary hover:text-white transition-colors">
                <i class="fa-solid fa-file-excel"></i>
                {{ __('admin/product.import.download_template') }}
            </a>
            <a href="{{ route('admin.products.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                {{ __('admin/product.import.back') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
        <section class="xl:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-5 md:p-7">
            <form action="{{ route('admin.products.import.preview') }}" method="POST" enctype="multipart/form-data"
                class="space-y-6" data-submit-once>
                @csrf

                <div>
                    <label for="product-import-file" class="block text-sm font-semibold text-gray-900 mb-2">
                        {{ __('admin/product.import.file') }} <span class="text-red-500">*</span>
                    </label>
                    <label for="product-import-file"
                        class="flex flex-col items-center justify-center gap-3 min-h-48 px-5 py-8 text-center border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 hover:border-primary/50 hover:bg-primary-soft/30 transition-colors cursor-pointer">
                        <span class="w-14 h-14 rounded-2xl bg-primary-soft text-primary flex items-center justify-center">
                            <i class="fa-solid fa-cloud-arrow-up text-2xl"></i>
                        </span>
                        <span class="text-sm font-semibold text-gray-800">{{ __('admin/product.import.choose_file') }}</span>
                        <span class="text-xs text-gray-500">{{ __('admin/product.import.upload_hint') }}</span>
                        <input id="product-import-file" type="file" name="file" accept=".xlsx,.csv,.txt" required class="sr-only">
                    </label>
                    @error('file')
                        <div class="mt-3 space-y-1 text-sm text-red-600">
                            @foreach ((array) $message as $item)
                                <p><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $item }}</p>
                            @endforeach
                        </div>
                    @enderror
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-2 border-t border-gray-100">
                    <a href="{{ route('admin.products.index') }}"
                        class="inline-flex justify-center items-center px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        {{ __('common.actions.cancel') }}
                    </a>
                    <button type="submit"
                        class="inline-flex justify-center items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-primary rounded-lg hover:bg-primary-hover transition-colors">
                        <i class="fa-solid fa-file-import"></i>
                        {{ __('admin/product.import.submit') }}
                    </button>
                </div>
            </form>
        </section>

        <aside class="space-y-5">
            <section class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-center gap-3 mb-4">
                    <span class="w-10 h-10 rounded-xl bg-accent-soft text-accent flex items-center justify-center">
                        <i class="fa-solid fa-list-check"></i>
                    </span>
                    <h2 class="font-semibold text-gray-900">{{ __('admin/product.import.rules_title') }}</h2>
                </div>
                <ul class="space-y-3 text-sm text-gray-600">
                    @foreach (['upsert', 'lookup', 'variant', 'transaction', 'images'] as $rule)
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check mt-0.5 text-green-500"></i>
                            <span>{{ __('admin/product.import.rules.' . $rule) }}</span>
                        </li>
                    @endforeach
                </ul>
            </section>

            <section class="bg-primary text-white rounded-xl shadow-sm p-5">
                <div class="flex items-center gap-3 mb-4">
                    <span class="w-10 h-10 rounded-xl bg-white/10 text-accent flex items-center justify-center">
                        <i class="fa-solid fa-table-cells"></i>
                    </span>
                    <h2 class="font-semibold">{{ __('admin/product.import.columns_title') }}</h2>
                </div>
                <ul class="space-y-3 text-sm text-white/75">
                    @foreach (['products', 'variants', 'specifications'] as $sheet)
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-angle-right mt-0.5 text-accent"></i>
                            <span>{{ __('admin/product.import.columns.' . $sheet) }}</span>
                        </li>
                    @endforeach
                </ul>
            </section>
        </aside>
    </div>
@endsection
