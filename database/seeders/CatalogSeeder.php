<?php

namespace Database\Seeders;

use App\Const\ProductConst;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductGallery;
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

            if ($item['has_variants']) {
                $this->seedVariants($product->load('galleries'), $item, $attributeValues);
            }
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
            'Màu sắc' => ['Đen', 'Trắng', 'Xanh dương', 'Vàng đồng'],
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

    protected function seedVariants(Product $product, array $item, array $attributeValues): void
    {
        $matrix = $this->variantMatrix($item['category']);
        $gallery = $product->galleries->pluck('image')->values();
        $keptIds = [];

        foreach ($matrix as $position => $combo) {
            $price = (int) ($item['price'] + $combo['extra']);
            $sale = $item['sale_price'] ? (int) ($item['sale_price'] + $combo['extra']) : null;

            $variant = ProductVariant::updateOrCreate(
                ['sku' => $product->sku . '-' . $combo['code']],
                [
                    'product_id' => $product->id,
                    'price' => $price,
                    'sale_price' => $sale,
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
        ]);
    }

    protected function variantMatrix(string $category): array
    {
        $storages = $category === 'Laptop'
            ? ['256GB', '512GB', '1TB']
            : ['128GB', '256GB', '512GB'];

        $colors = ['Đen', 'Trắng', 'Xanh dương'];
        $matrix = [];

        foreach ($storages as $i => $storage) {
            foreach (array_slice($colors, 0, 2) as $j => $color) {
                $matrix[] = [
                    'code' => 'V' . ($i + 1) . ($j + 1),
                    'values' => [$storage, $color],
                    'extra' => ($i * 3000000) + ($j * 500000),
                ];
            }
        }

        return $matrix;
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
