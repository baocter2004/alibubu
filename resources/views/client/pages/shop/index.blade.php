@extends('client.layouts.app')

@section('title', 'Alibubu - Tất cả sản phẩm')

@section('content')
    <nav class="flex items-center gap-2 text-sm text-muted-foreground mb-6">
        <a href="{{ route('index') }}" class="hover:text-primary transition-colors">Trang chủ</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-foreground font-medium">Sản phẩm</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-6">
        <aside class="lg:w-64 shrink-0">
            <button type="button" id="filter-toggle"
                class="lg:hidden w-full flex items-center justify-between px-4 py-3 bg-card border border-border rounded-xl font-medium mb-3">
                <span><i class="fa-solid fa-sliders mr-2 text-primary"></i>Bộ lọc</span>
                <i class="fa-solid fa-chevron-down text-xs"></i>
            </button>

            <form action="{{ route('shop.index') }}" method="GET" id="filter-panel"
                class="hidden lg:block bg-card border border-border rounded-xl p-5 space-y-6 lg:sticky lg:top-24">
                <div>
                    <label for="keyword" class="block text-sm font-semibold text-foreground mb-2">Từ khoá</label>
                    <div class="relative">
                        <i
                            class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground"></i>
                        <input type="search" id="keyword" name="keyword" value="{{ request('keyword') }}"
                            placeholder="Tên sản phẩm..."
                            class="w-full pl-9 pr-3 py-2 text-sm border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    </div>
                </div>

                <div>
                    <p class="text-sm font-semibold text-foreground mb-2">Danh mục</p>
                    <div class="space-y-1 max-h-56 overflow-y-auto pr-1">
                        <label
                            class="flex items-center gap-2 px-2 py-1.5 rounded-lg cursor-pointer hover:bg-muted transition-colors">
                            <input type="radio" name="category_id" value=""
                                @checked(! request('category_id')) class="accent-primary">
                            <span class="text-sm text-muted-foreground">Tất cả</span>
                        </label>
                        @foreach ($categories as $category)
                            <label
                                class="flex items-center gap-2 px-2 py-1.5 rounded-lg cursor-pointer hover:bg-muted transition-colors">
                                <input type="radio" name="category_id" value="{{ $category->id }}"
                                    @checked((int) request('category_id') === $category->id) class="accent-primary">
                                <span class="text-sm text-muted-foreground">{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label for="branch_id" class="block text-sm font-semibold text-foreground mb-2">Thương hiệu</label>
                    <select id="branch_id" name="branch_id"
                        class="w-full px-3 py-2 text-sm border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        <option value="">Tất cả thương hiệu</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @selected((int) request('branch_id') === $branch->id)>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <p class="text-sm font-semibold text-foreground mb-2">Khoảng giá</p>
                    <div class="flex items-center gap-2">
                        <input type="number" name="min_price" min="0" step="100000"
                            value="{{ request('min_price') }}" placeholder="Từ"
                            class="w-full px-3 py-2 text-sm border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        <span class="text-muted-foreground">-</span>
                        <input type="number" name="max_price" min="0" step="100000"
                            value="{{ request('max_price') }}" placeholder="Đến"
                            class="w-full px-3 py-2 text-sm border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    </div>
                    @error('max_price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_sale" value="1" @checked(request('is_sale'))
                        class="h-4 w-4 rounded accent-primary">
                    <span class="text-sm text-muted-foreground">Chỉ hiện sản phẩm giảm giá</span>
                </label>

                <input type="hidden" name="sort" value="{{ request('sort') }}">

                <div class="flex gap-2 pt-1">
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors">
                        Áp dụng
                    </button>
                    <a href="{{ route('shop.index') }}"
                        class="px-4 py-2.5 text-sm font-medium text-muted-foreground border border-border rounded-lg hover:bg-muted transition-colors">
                        Xoá lọc
                    </a>
                </div>
            </form>
        </aside>

        <div class="flex-1 min-w-0">
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-card border border-border rounded-xl px-4 py-3 mb-5">
                <div>
                    <h1 class="text-lg font-bold text-foreground">Tất cả sản phẩm</h1>
                    <p class="text-sm text-muted-foreground">
                        Tìm thấy <span class="font-semibold text-foreground">{{ $products->total() }}</span> sản phẩm
                    </p>
                </div>

                <form action="{{ route('shop.index') }}" method="GET" class="flex items-center gap-2">
                    @foreach (Arr::except($filters, ['sort']) as $name => $value)
                        @if ($value !== null && $value !== '')
                            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                        @endif
                    @endforeach

                    <label for="sort" class="text-sm text-muted-foreground whitespace-nowrap">Sắp xếp</label>
                    <select id="sort" name="sort" onchange="this.form.submit()"
                        class="px-3 py-2 text-sm border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        @foreach (['newest' => 'Mới nhất', 'popular' => 'Xem nhiều', 'price_asc' => 'Giá tăng dần', 'price_desc' => 'Giá giảm dần', 'oldest' => 'Cũ nhất'] as $value => $label)
                            <option value="{{ $value }}" @selected(request('sort', 'newest') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            @if ($products->isEmpty())
                <div class="bg-card border border-dashed border-border rounded-xl py-20 text-center">
                    <i class="fa-solid fa-magnifying-glass text-5xl text-muted-foreground/25 mb-4"></i>
                    <p class="text-lg font-semibold text-foreground mb-1">Không tìm thấy sản phẩm</p>
                    <p class="text-sm text-muted-foreground mb-5">Thử thay đổi bộ lọc hoặc từ khoá tìm kiếm.</p>
                    <a href="{{ route('shop.index') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors">
                        <i class="fa-solid fa-rotate-left"></i> Xoá bộ lọc
                    </a>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach ($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>

                @include('components.pagination', ['paginator' => $products->withQueryString()])
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#filter-toggle').on('click', function() {
                $('#filter-panel').toggleClass('hidden');
                $(this).find('.fa-chevron-down').toggleClass('rotate-180');
            });
        });
    </script>
@endpush
