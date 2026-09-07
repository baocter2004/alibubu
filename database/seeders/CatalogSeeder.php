<?php

namespace Database\Seeders;

use App\Const\ProductConst;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductGallery;
use App\Models\ProductSpecification;
use App\Models\ProductVariant;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    protected array $categoryIcons = [
        'Điện thoại' => 'fa-solid fa-mobile-screen-button',
        'Laptop' => 'fa-solid fa-laptop',
        'Máy tính bảng' => 'fa-solid fa-tablet-screen-button',
        'Phụ kiện' => 'fa-solid fa-plug',
        'Đồng hồ thông minh' => 'fa-solid fa-stopwatch',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = require database_path('seeders/data/products.php');

        $branches = $this->seedBranches($items);
        $categories = $this->seedCategories($items);
        $tags = $this->seedTags($items);
        $attributeValues = $this->seedAttributes();

        foreach ($items as $index => $item) {
            $product = Product::updateOrCreate(
                ['slug' => Str::slug(Str::ascii($item['name']))],
                [
                    'branch_id' => $branches[$item['brand']]->id,
                    'name' => $item['name'],
                    'views' => random_int(80, 6500),
                    'rating' => $item['rating'] ?? 0,
                    'stock' => $item['stock'] ?? 0,
                    'sold' => random_int(5, 480),
                    'short_descriptions' => Str::limit($item['short'], 250),
                    'descriptions' => $item['description'],
                    'thumbnail' => $this->storeImage($item['thumbnail']),
                    'type' => $item['has_variants'] ? ProductConst::VARIANT : ProductConst::SINGLE,
                    'sku' => $item['sku'],
                    'price' => $item['price'],
                    'sale_price' => $item['sale_price'],
                    'sale_price_start_at' => $item['sale_price'] ? now()->subDays(5) : null,
                    'sale_price_end_at' => $item['sale_price'] ? now()->addDays(25) : null,
                    'is_sale' => (bool) $item['sale_price'],
                    'is_featured' => $index % 4 === 0,
                    'is_trending' => ($item['rating'] ?? 0) >= 4,
                    'is_active' => true,
                ]
            );

            $product->categories()->sync([$categories[$item['category']]->id]);
            $product->tags()->sync(collect($item['tags'])->map(fn ($tag) => $tags[$tag]->id)->all());

            $this->seedGallery($product, $item['gallery']);
            $this->seedSpecifications($product, $item);

            if ($item['has_variants']) {
                $this->seedVariants($product->load('galleries'), $item, $attributeValues);
            }
        }

        $this->seedAccessories();
    }

    protected function seedAccessories(): void
    {
        $accessoryIds = Product::query()
            ->whereHas('categories', fn ($query) => $query->where('name', 'Phụ kiện'))
            ->pluck('id');

        if ($accessoryIds->isEmpty()) {
            return;
        }

        $targets = Product::query()
            ->whereHas('categories', fn ($query) => $query->whereIn('name', ['Điện thoại', 'Laptop', 'Máy tính bảng']))
            ->get();

        foreach ($targets as $product) {
            if ($product->accessories()->exists()) {
                continue;
            }

            $product->accessories()->sync($accessoryIds->shuffle()->take(4)->all());
        }
    }

    protected function seedBranches(array $items): array
    {
        $branches = [];

        foreach (collect($items)->pluck('brand')->unique() as $name) {
            $branches[$name] = Branch::updateOrCreate(
                ['slug' => Str::slug(Str::ascii($name))],
                [
                    'name' => $name,
                    'logo' => null,
                    'is_active' => true,
                ]
            );
        }

        return $branches;
    }

    protected function seedCategories(array $items): array
    {
        $categories = [];
        $ordinal = 0;

        foreach (collect($items)->pluck('category')->unique() as $name) {
            $categories[$name] = Category::updateOrCreate(
                ['slug' => Str::slug(Str::ascii($name))],
                [
                    'name' => $name,
                    'icon' => $this->categoryIcons[$name] ?? 'fa-solid fa-tag',
                    'ordinal' => $ordinal++,
                    'is_active' => true,
                ]
            );
        }

        return $categories;
    }

    protected function seedTags(array $items): array
    {
        $tags = [];

        foreach (collect($items)->pluck('tags')->flatten()->unique() as $name) {
            $tags[$name] = Tag::updateOrCreate(
                ['slug' => Str::slug(Str::ascii($name))],
                ['name' => Str::title($name)]
            );
        }

        return $tags;
    }

    protected function seedAttributes(): array
    {
        $values = [];

        $attributes = [
            'Dung lượng' => ['128GB', '256GB', '512GB', '1TB'],
            'Màu sắc' => ['Đen', 'Trắng', 'Bạc', 'Xanh dương', 'Vàng đồng'],
            'Kích thước' => ['41mm', '45mm'],
            'Phiên bản' => ['Tiêu chuẩn'],
        ];

        foreach ($attributes as $name => $options) {
            $attribute = Attribute::updateOrCreate(
                ['slug' => Str::slug(Str::ascii($name))],
                ['name' => $name, 'is_active' => true]
            );

            foreach ($options as $option) {
                $values[$option] = AttributeValue::updateOrCreate(
                    ['value' => $option],
                    ['attribute_id' => $attribute->id, 'is_active' => true]
                );
            }
        }

        return $values;
    }

    protected function seedGallery(Product $product, array $gallery): void
    {
        if ($product->galleries()->exists()) {
            return;
        }

        foreach ($gallery as $image) {
            $stored = $this->storeImage($image);

            if ($stored) {
                ProductGallery::create(['product_id' => $product->id, 'image' => $stored]);
            }
        }
    }

    protected function seedSpecifications(Product $product, array $item): void
    {
        if ($product->specifications()->exists()) {
            return;
        }

        $specs = [
            ['group' => 'Tổng quan', 'name' => 'Thương hiệu', 'value' => $item['brand']],
            ['group' => 'Tổng quan', 'name' => 'Danh mục', 'value' => $item['category']],
            ['group' => 'Tổng quan', 'name' => 'Mã sản phẩm', 'value' => $item['sku'] ?? '-'],
            ['group' => 'Bảo hành', 'name' => 'Chính sách', 'value' => $item['short']],
            ['group' => 'Kho hàng', 'name' => 'Tồn kho', 'value' => (string) ($item['stock'] ?? 0)],
        ];

        foreach ($specs as $ordinal => $spec) {
            ProductSpecification::create(array_merge($spec, [
                'product_id' => $product->id,
                'ordinal' => $ordinal,
            ]));
        }
    }

    protected function seedVariants(Product $product, array $item, array $attributeValues): void
    {
        $matrix = $this->variantMatrix($item['category']);
        $gallery = $product->galleries->pluck('image')->values();
        $keptIds = [];
        $baseStock = intdiv((int) ($item['stock'] ?? 0), max(count($matrix), 1));
        $stockRemainder = (int) ($item['stock'] ?? 0) % max(count($matrix), 1);

        foreach ($matrix as $position => $combo) {
            $price = (int) ($item['price'] + $combo['extra']);
            $sale = $item['sale_price'] ? (int) ($item['sale_price'] + $combo['extra']) : null;

            $variant = ProductVariant::updateOrCreate(
                ['sku' => $product->sku . '-' . $combo['code']],
                [
                    'product_id' => $product->id,
                    'price' => $price,
                    'sale_price' => $sale,
                    'stock' => $baseStock + ($position < $stockRemainder ? 1 : 0),
                    'thumbnail' => $gallery[$position % max($gallery->count(), 1)] ?? $product->thumbnail,
                    'is_active' => true,
                ]
            );

            $variant->attributeValues()->sync(
                collect($combo['values'])
                    ->map(fn ($value) => $attributeValues[$value]->id ?? null)
                    ->filter()
                    ->values()
                    ->all()
            );

            $keptIds[] = $variant->id;
        }

        $product->variants()->whereNotIn('id', $keptIds)->delete();

        $prices = collect($matrix)->map(fn ($combo) => (int) ($item['price'] + $combo['extra']));

        $product->update([
            'price' => $prices->min(),
            'sale_price' => $item['sale_price'] ? (int) ($item['sale_price'] + collect($matrix)->min('extra')) : null,
            'stock' => (int) $product->variants()->sum('stock'),
        ]);
    }

    protected function variantMatrix(string $category): array
    {
        [$primary, $secondary] = match ($category) {
            'Laptop' => [['256GB', '512GB', '1TB'], ['Đen', 'Bạc']],
            'Đồng hồ thông minh' => [['41mm', '45mm'], ['Đen', 'Bạc', 'Vàng đồng']],
            'Phụ kiện' => [['Tiêu chuẩn'], ['Đen', 'Trắng', 'Xanh dương']],
            default => [['128GB', '256GB', '512GB'], ['Đen', 'Trắng']],
        };

        $matrix = [];

        foreach ($primary as $i => $first) {
            foreach ($secondary as $j => $second) {
                $matrix[] = [
                    'code' => 'V' . ($i + 1) . ($j + 1),
                    'values' => [$first, $second],
                    'extra' => ($i * $this->variantStep($category)) + ($j * 300000),
                ];
            }
        }

        return $matrix;
    }

    protected function variantStep(string $category): int
    {
        return match ($category) {
            'Laptop' => 3000000,
            'Đồng hồ thông minh' => 1200000,
            'Phụ kiện' => 0,
            default => 2000000,
        };
    }

    protected function storeImage(?string $file): ?string
    {
        if (! $file) {
            return null;
        }

        $source = base_path('images/products/' . $file);

        if (! File::exists($source)) {
            return null;
        }

        $target = 'products/' . $file;

        if (! Storage::disk('public')->exists($target)) {
            Storage::disk('public')->put($target, File::get($source));
        }

        return $target;
    }
}
