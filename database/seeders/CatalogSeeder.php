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
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = $this->seedBranches();
        $categories = $this->seedCategories();
        $tags = $this->seedTags();
        $attributeValues = $this->seedAttributes();

        foreach ($this->products() as $index => $item) {
            $branch = $branches[$item['branch']];
            $category = $categories[$item['category']];

            $product = Product::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'branch_id' => $branch->id,
                    'name' => $item['name'],
                    'views' => random_int(50, 4000),
                    'short_descriptions' => $item['short'],
                    'descriptions' => $item['description'],
                    'thumbnail' => null,
                    'type' => $item['variants'] ? ProductConst::VARIANT : ProductConst::SINGLE,
                    'sku' => 'ALB-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                    'price' => $item['price'],
                    'sale_price' => $item['sale_price'],
                    'sale_price_start_at' => $item['sale_price'] ? now()->subDays(3) : null,
                    'sale_price_end_at' => $item['sale_price'] ? now()->addDays(20) : null,
                    'is_sale' => (bool) $item['sale_price'],
                    'is_featured' => $item['featured'],
                    'is_trending' => $item['trending'],
                    'is_active' => true,
                ]
            );

            $product->categories()->sync([$category->id]);
            $product->tags()->sync(collect($item['tags'])->map(fn ($tag) => $tags[$tag]->id)->all());

            if ($product->galleries()->doesntExist()) {
                foreach (range(1, 3) as $position) {
                    ProductGallery::create([
                        'product_id' => $product->id,
                        'image' => null,
                    ]);
                }
            }

            if (! $item['variants']) {
                continue;
            }

            foreach ($item['variants'] as $position => $variantName) {
                $variant = ProductVariant::updateOrCreate(
                    ['sku' => $product->sku . '-V' . ($position + 1)],
                    [
                        'product_id' => $product->id,
                        'price' => $item['price'] + ($position * 500000),
                        'sale_price' => $item['sale_price'] ? $item['sale_price'] + ($position * 500000) : null,
                        'thumbnail' => '',
                        'is_active' => true,
                    ]
                );

                if (isset($attributeValues[$variantName])) {
                    $variant->attributeValues()->sync([$attributeValues[$variantName]->id]);
                }
            }
        }
    }

    protected function seedBranches(): array
    {
        $branches = [];

        foreach (['Apple', 'Samsung', 'Xiaomi', 'Sony', 'Asus'] as $name) {
            $branches[$name] = Branch::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'logo' => '',
                    'is_active' => true,
                ]
            );
        }

        return $branches;
    }

    protected function seedCategories(): array
    {
        $categories = [];

        $tree = [
            'Điện thoại' => 'fa-solid fa-mobile-screen',
            'Laptop' => 'fa-solid fa-laptop',
            'Máy tính bảng' => 'fa-solid fa-tablet-screen-button',
            'Âm thanh' => 'fa-solid fa-headphones',
            'Phụ kiện' => 'fa-solid fa-plug',
        ];

        $ordinal = 0;

        foreach ($tree as $name => $icon) {
            $categories[$name] = Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'icon' => $icon,
                    'ordinal' => $ordinal++,
                    'is_active' => true,
                ]
            );
        }

        return $categories;
    }

    protected function seedTags(): array
    {
        $tags = [];

        foreach (['Bán chạy', 'Mới về', 'Flash Sale', 'Trả góp 0%'] as $name) {
            $tags[$name] = Tag::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }

        return $tags;
    }

    protected function seedAttributes(): array
    {
        $values = [];

        $attributes = [
            'Dung lượng' => ['128GB', '256GB', '512GB'],
            'Màu sắc' => ['Đen', 'Trắng', 'Xanh dương'],
        ];

        foreach ($attributes as $name => $options) {
            $attribute = Attribute::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'is_active' => true,
                ]
            );

            foreach ($options as $option) {
                $values[$option] = AttributeValue::updateOrCreate(
                    ['value' => $option],
                    [
                        'attribute_id' => $attribute->id,
                        'is_active' => true,
                    ]
                );
            }
        }

        return $values;
    }

    protected function products(): array
    {
        return [
            [
                'name' => 'iPhone 16 Pro Max',
                'branch' => 'Apple',
                'category' => 'Điện thoại',
                'tags' => ['Bán chạy', 'Trả góp 0%'],
                'price' => 34990000,
                'sale_price' => 31990000,
                'featured' => true,
                'trending' => true,
                'short' => 'Chip A18 Pro, khung titan, camera 48MP.',
                'description' => 'iPhone 16 Pro Max sở hữu khung titan siêu nhẹ, màn hình Super Retina XDR 6.9 inch và chip A18 Pro cho hiệu năng vượt trội. Hệ thống camera 48MP nâng cấp khả năng quay video 4K 120fps.',
                'variants' => ['256GB', '512GB'],
            ],
            [
                'name' => 'Samsung Galaxy S25 Ultra',
                'branch' => 'Samsung',
                'category' => 'Điện thoại',
                'tags' => ['Bán chạy', 'Mới về'],
                'price' => 33990000,
                'sale_price' => 29990000,
                'featured' => true,
                'trending' => true,
                'short' => 'Bút S Pen, camera 200MP, màn hình 6.9 inch.',
                'description' => 'Galaxy S25 Ultra là flagship mạnh mẽ nhất của Samsung với camera 200MP, bút S Pen tích hợp và màn hình Dynamic AMOLED 2X 120Hz.',
                'variants' => ['256GB', '512GB'],
            ],
            [
                'name' => 'Xiaomi 15 Pro',
                'branch' => 'Xiaomi',
                'category' => 'Điện thoại',
                'tags' => ['Flash Sale'],
                'price' => 22990000,
                'sale_price' => 19990000,
                'featured' => false,
                'trending' => true,
                'short' => 'Snapdragon 8 Elite, sạc nhanh 90W.',
                'description' => 'Xiaomi 15 Pro trang bị chip Snapdragon 8 Elite, camera Leica và công nghệ sạc nhanh HyperCharge 90W.',
                'variants' => ['256GB', '512GB'],
            ],
            [
                'name' => 'MacBook Air M4 13 inch',
                'branch' => 'Apple',
                'category' => 'Laptop',
                'tags' => ['Bán chạy', 'Trả góp 0%'],
                'price' => 27990000,
                'sale_price' => 25490000,
                'featured' => true,
                'trending' => false,
                'short' => 'Chip M4, pin 18 giờ, nặng 1.24kg.',
                'description' => 'MacBook Air M4 mỏng nhẹ với thời lượng pin lên tới 18 giờ, màn hình Liquid Retina và hiệu năng ổn định cho công việc hằng ngày.',
                'variants' => ['256GB', '512GB'],
            ],
            [
                'name' => 'Asus ROG Zephyrus G16',
                'branch' => 'Asus',
                'category' => 'Laptop',
                'tags' => ['Mới về'],
                'price' => 52990000,
                'sale_price' => null,
                'featured' => false,
                'trending' => false,
                'short' => 'RTX 5070, màn OLED 240Hz.',
                'description' => 'Laptop gaming cao cấp với card RTX 5070, màn hình OLED 240Hz và hệ thống tản nhiệt buồng hơi.',
                'variants' => null,
            ],
            [
                'name' => 'iPad Air M3 11 inch',
                'branch' => 'Apple',
                'category' => 'Máy tính bảng',
                'tags' => ['Bán chạy'],
                'price' => 16990000,
                'sale_price' => 15490000,
                'featured' => true,
                'trending' => false,
                'short' => 'Chip M3, hỗ trợ Apple Pencil Pro.',
                'description' => 'iPad Air M3 cân bằng giữa hiệu năng và tính di động, hỗ trợ Apple Pencil Pro và Magic Keyboard.',
                'variants' => ['128GB', '256GB'],
            ],
            [
                'name' => 'Samsung Galaxy Tab S10',
                'branch' => 'Samsung',
                'category' => 'Máy tính bảng',
                'tags' => ['Mới về'],
                'price' => 18990000,
                'sale_price' => null,
                'featured' => false,
                'trending' => false,
                'short' => 'Màn AMOLED 11 inch, kèm S Pen.',
                'description' => 'Galaxy Tab S10 với màn hình AMOLED 11 inch, loa AKG bốn chiều và bút S Pen đi kèm.',
                'variants' => null,
            ],
            [
                'name' => 'Sony WH-1000XM6',
                'branch' => 'Sony',
                'category' => 'Âm thanh',
                'tags' => ['Bán chạy', 'Flash Sale'],
                'price' => 9490000,
                'sale_price' => 7990000,
                'featured' => true,
                'trending' => true,
                'short' => 'Chống ồn chủ động, pin 40 giờ.',
                'description' => 'Tai nghe chụp tai Sony WH-1000XM6 với khả năng chống ồn hàng đầu, pin 40 giờ và chất âm Hi-Res.',
                'variants' => ['Đen', 'Trắng'],
            ],
            [
                'name' => 'AirPods Pro 3',
                'branch' => 'Apple',
                'category' => 'Âm thanh',
                'tags' => ['Bán chạy'],
                'price' => 6290000,
                'sale_price' => 5590000,
                'featured' => false,
                'trending' => true,
                'short' => 'Chống ồn thích ứng, sạc USB-C.',
                'description' => 'AirPods Pro 3 nâng cấp khả năng chống ồn thích ứng, âm thanh không gian và hộp sạc USB-C.',
                'variants' => null,
            ],
            [
                'name' => 'Sạc nhanh Anker 65W GaN',
                'branch' => 'Xiaomi',
                'category' => 'Phụ kiện',
                'tags' => ['Flash Sale'],
                'price' => 890000,
                'sale_price' => 690000,
                'featured' => false,
                'trending' => false,
                'short' => 'Ba cổng, công nghệ GaN nhỏ gọn.',
                'description' => 'Củ sạc GaN 65W ba cổng, sạc đồng thời laptop và điện thoại, kích thước nhỏ gọn tiện mang theo.',
                'variants' => null,
            ],
            [
                'name' => 'Ốp lưng MagSafe trong suốt',
                'branch' => 'Apple',
                'category' => 'Phụ kiện',
                'tags' => ['Mới về'],
                'price' => 1290000,
                'sale_price' => null,
                'featured' => false,
                'trending' => false,
                'short' => 'Chống ố vàng, tương thích MagSafe.',
                'description' => 'Ốp lưng trong suốt tích hợp nam châm MagSafe, chống ố vàng và bảo vệ viền camera.',
                'variants' => null,
            ],
            [
                'name' => 'Chuột Logitech MX Master 4',
                'branch' => 'Asus',
                'category' => 'Phụ kiện',
                'tags' => ['Bán chạy'],
                'price' => 2790000,
                'sale_price' => 2390000,
                'featured' => false,
                'trending' => false,
                'short' => 'Cuộn MagSpeed, kết nối 3 thiết bị.',
                'description' => 'Chuột không dây cao cấp với con lăn MagSpeed, cảm biến 8000 DPI và khả năng kết nối cùng lúc ba thiết bị.',
                'variants' => null,
            ],
        ];
    }
}
