<?php

namespace App\Services\Admin;

use App\Const\ProductConst;
use App\Exceptions\ProductImportException;
use App\Models\AttributeValue;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSpecification;
use App\Models\ProductVariant;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use ZipArchive;

class ProductImportService
{
    public const MAX_PRODUCTS = 500;

    public const MAX_VARIANTS = 2500;

    public const MAX_SPECIFICATIONS = 5000;

    private const PRODUCT_HEADERS = [
        'product_sku', 'product_name', 'brand', 'categories', 'type', 'stock',
        'price', 'sale_price', 'sale_start_at', 'sale_end_at', 'short_description',
        'description', 'thumbnail', 'tags', 'is_active', 'is_featured', 'is_trending',
    ];

    private const VARIANT_HEADERS = [
        'product_sku', 'variant_sku', 'attribute_values', 'price', 'sale_price', 'stock', 'is_active',
    ];

    private const SPECIFICATION_HEADERS = ['product_sku', 'group', 'name', 'value', 'ordinal'];

    public function import(UploadedFile $file): array
    {
        $analysis = $this->analyse($file);

        return DB::transaction(fn () => $this->persist(
            $analysis['products'],
            $analysis['variants'],
            $analysis['specifications'],
            $analysis['lookups']
        ));
    }

    public function preview(UploadedFile $file): array
    {
        $analysis = $this->analyse($file);

        $skus = collect($analysis['products'])->pluck('sku')->filter()->values();
        $existing = $skus->isEmpty()
            ? collect()
            : Product::withTrashed()->whereIn('sku', $skus)->pluck('sku')->all();

        $brands = Branch::query()->pluck('name', 'id');

        $rows = collect($analysis['products'])->map(fn (array $row) => [
            'sku' => $row['sku'] ?? '-',
            'name' => $row['name'] ?? '-',
            'brand' => $brands[$row['branch_id'] ?? null] ?? null,
            'price' => $row['price'] ?? null,
            'stock' => $row['stock'] ?? null,
            'variants' => collect($analysis['variants'])->where('product_sku', $row['sku'] ?? null)->count(),
            'is_update' => in_array($row['sku'] ?? null, $existing, true),
        ]);

        return [
            'rows' => $rows->all(),
            'counts' => [
                'products' => count($analysis['products']),
                'variants' => count($analysis['variants']),
                'specifications' => count($analysis['specifications']),
                'create' => $rows->where('is_update', false)->count(),
                'update' => $rows->where('is_update', true)->count(),
            ],
        ];
    }

    protected function analyse(UploadedFile $file): array
    {
        $sheets = $this->readWorkbook($file);
        $products = $this->rowsFromSheet($sheets, 'Products');
        $variants = $this->rowsFromSheet($sheets, 'Variants');
        $specifications = $this->rowsFromSheet($sheets, 'Specifications');

        $errors = [];

        if ($products === []) {
            $errors[] = __('admin/product.import.errors.missing_products_sheet');
        }

        if (count($products) > self::MAX_PRODUCTS) {
            $errors[] = __('admin/product.import.errors.too_many_products', ['max' => self::MAX_PRODUCTS]);
        }

        if (count($variants) > self::MAX_VARIANTS) {
            $errors[] = __('admin/product.import.errors.too_many_variants', ['max' => self::MAX_VARIANTS]);
        }

        if (count($specifications) > self::MAX_SPECIFICATIONS) {
            $errors[] = __('admin/product.import.errors.too_many_specifications', ['max' => self::MAX_SPECIFICATIONS]);
        }

        if ($errors !== []) {
            throw new ProductImportException($errors);
        }

        $lookups = $this->lookups();
        $preparedProducts = $this->prepareProducts($products, $lookups);
        $preparedVariants = $this->prepareVariants($variants, $preparedProducts, $lookups);
        $preparedSpecifications = $this->prepareSpecifications($specifications, $preparedProducts);
        $errors = array_merge(
            $preparedProducts['errors'],
            $preparedVariants['errors'],
            $preparedSpecifications['errors']
        );

        $this->validateCrossReferences($preparedProducts['rows'], $preparedVariants['rows'], $preparedSpecifications['rows'], $errors);

        if ($errors !== []) {
            throw new ProductImportException($errors);
        }

        return [
            'products' => $preparedProducts['rows'],
            'variants' => $preparedVariants['rows'],
            'specifications' => $preparedSpecifications['rows'],
            'lookups' => $lookups,
        ];
    }

    protected function prepareProducts(array $rows, array $lookups): array
    {
        $prepared = [];
        $errors = [];
        $seen = [];

        foreach ($rows as $rowNumber => $raw) {
            $row = $this->normalizeRow($raw);
            $sku = trim((string) ($row['product_sku'] ?? ''));
            $name = trim((string) ($row['product_name'] ?? ''));
            $type = $this->productType($row['type'] ?? null);
            $branch = $this->lookup($lookups['branches'], $row['brand'] ?? null);
            $categories = $this->splitList($row['categories'] ?? null);
            $categoryModels = collect($categories)
                ->map(fn ($name) => $this->lookup($lookups['categories'], $name))
                ->filter();

            if ($sku === '') {
                $errors[] = $this->error($rowNumber, 'product_sku', __('admin/product.import.errors.required'));
            } elseif (isset($seen[$this->key($sku)])) {
                $errors[] = $this->error($rowNumber, 'product_sku', __('admin/product.import.errors.duplicate_in_file'));
            } else {
                $seen[$this->key($sku)] = true;
            }

            if ($name === '') {
                $errors[] = $this->error($rowNumber, 'product_name', __('admin/product.import.errors.required'));
            }

            if (! $branch) {
                $errors[] = $this->error($rowNumber, 'brand', __('admin/product.import.errors.lookup', ['value' => $row['brand'] ?? '']));
            }

            if ($categoryModels->count() !== count($categories) || $categories === []) {
                $errors[] = $this->error($rowNumber, 'categories', __('admin/product.import.errors.lookup_list'));
            }

            if ($type === null) {
                $errors[] = $this->error($rowNumber, 'type', __('admin/product.import.errors.invalid_type'));
            }

            $stock = $this->number($row['stock'] ?? null);
            if (($type !== ProductConst::VARIANT && ($stock === null || $stock < 0 || floor($stock) !== $stock))
                || ($type === ProductConst::VARIANT && $stock !== null && ($stock < 0 || floor($stock) !== $stock))) {
                $errors[] = $this->error($rowNumber, 'stock', __('admin/product.import.errors.invalid_stock'));
            }

            $price = $this->number($row['price'] ?? null);
            $salePrice = $this->number($row['sale_price'] ?? null);
            if ($type === ProductConst::SINGLE && ($price === null || $price < 0)) {
                $errors[] = $this->error($rowNumber, 'price', __('admin/product.import.errors.required_price'));
            }
            if ($salePrice !== null && ($price === null || $salePrice < 0 || $salePrice >= $price)) {
                $errors[] = $this->error($rowNumber, 'sale_price', __('admin/product.import.errors.invalid_sale_price'));
            }

            $start = $this->date($row['sale_start_at'] ?? null);
            $end = $this->date($row['sale_end_at'] ?? null);
            if (($row['sale_start_at'] ?? '') !== '' && ! $start) {
                $errors[] = $this->error($rowNumber, 'sale_start_at', __('admin/product.import.errors.invalid_date'));
            }
            if (($row['sale_end_at'] ?? '') !== '' && ! $end) {
                $errors[] = $this->error($rowNumber, 'sale_end_at', __('admin/product.import.errors.invalid_date'));
            }
            if ($start && $end && $end->lt($start)) {
                $errors[] = $this->error($rowNumber, 'sale_end_at', __('admin/product.import.errors.end_before_start'));
            }

            $prepared[$this->key($sku)] = [
                'row' => $rowNumber,
                'sku' => $sku,
                'name' => $name,
                'branch_id' => $branch?->id,
                'category_ids' => $categoryModels->pluck('id')->values()->all(),
                'type' => $type,
                'stock' => (int) ($stock ?? 0),
                'price' => $price,
                'sale_price' => $salePrice,
                'sale_price_start_at' => $start?->toDateTimeString(),
                'sale_price_end_at' => $end?->toDateTimeString(),
                'short_descriptions' => $this->nullableString($row['short_description'] ?? null, 255),
                'descriptions' => $this->nullableString($row['description'] ?? null, 5000),
                'thumbnail' => $this->nullableString($row['thumbnail'] ?? null, 255),
                'tags' => $this->splitList($row['tags'] ?? null),
                'is_active' => $this->boolean($row['is_active'] ?? null, true),
                'is_featured' => $this->boolean($row['is_featured'] ?? null, false),
                'is_trending' => $this->boolean($row['is_trending'] ?? null, false),
            ];
        }

        return ['rows' => $prepared, 'errors' => $errors];
    }

    protected function prepareVariants(array $rows, array $products, array $lookups): array
    {
        $prepared = [];
        $errors = [];
        $seen = [];

        foreach ($rows as $rowNumber => $raw) {
            $row = $this->normalizeRow($raw);
            $productSku = trim((string) ($row['product_sku'] ?? ''));
            $variantSku = trim((string) ($row['variant_sku'] ?? ''));
            $product = $products['rows'][$this->key($productSku)] ?? null;
            $values = $this->parseAttributeValues($row['attribute_values'] ?? null, $lookups, $rowNumber, $errors);

            if (! $product) {
                $errors[] = $this->error($rowNumber, 'product_sku', __('admin/product.import.errors.unknown_product', ['value' => $productSku]));
            }
            if ($variantSku === '') {
                $errors[] = $this->error($rowNumber, 'variant_sku', __('admin/product.import.errors.required'));
            } elseif (isset($seen[$this->key($variantSku)])) {
                $errors[] = $this->error($rowNumber, 'variant_sku', __('admin/product.import.errors.duplicate_in_file'));
            } else {
                $seen[$this->key($variantSku)] = true;
            }

            $price = $this->number($row['price'] ?? null);
            $salePrice = $this->number($row['sale_price'] ?? null);
            if ($price === null || $price < 0) {
                $errors[] = $this->error($rowNumber, 'price', __('admin/product.import.errors.required_price'));
            }
            if ($salePrice !== null && ($salePrice < 0 || $salePrice >= $price)) {
                $errors[] = $this->error($rowNumber, 'sale_price', __('admin/product.import.errors.invalid_sale_price'));
            }

            $stock = $this->number($row['stock'] ?? null);
            if ($stock === null || $stock < 0 || floor($stock) !== $stock) {
                $errors[] = $this->error($rowNumber, 'stock', __('admin/product.import.errors.invalid_stock'));
            }

            $prepared[] = [
                'row' => $rowNumber,
                'product_sku' => $productSku,
                'variant_sku' => $variantSku,
                'attribute_value_ids' => $values,
                'price' => $price,
                'sale_price' => $salePrice,
                'stock' => (int) ($stock ?? 0),
                'is_active' => $this->boolean($row['is_active'] ?? null, true),
            ];
        }

        return ['rows' => $prepared, 'errors' => $errors];
    }

    protected function prepareSpecifications(array $rows, array $products): array
    {
        $prepared = [];
        $errors = [];

        foreach ($rows as $rowNumber => $raw) {
            $row = $this->normalizeRow($raw);
            $productSku = trim((string) ($row['product_sku'] ?? ''));
            if (! isset($products['rows'][$this->key($productSku)])) {
                $errors[] = $this->error($rowNumber, 'product_sku', __('admin/product.import.errors.unknown_product', ['value' => $productSku]));
            }

            foreach (['group', 'name', 'value'] as $field) {
                if (trim((string) ($row[$field] ?? '')) === '') {
                    $errors[] = $this->error($rowNumber, $field, __('admin/product.import.errors.required'));
                }
            }

            $ordinal = $this->number($row['ordinal'] ?? null);
            $prepared[] = [
                'row' => $rowNumber,
                'product_sku' => $productSku,
                'group' => $this->nullableString($row['group'] ?? null, 100),
                'name' => $this->nullableString($row['name'] ?? null, 120),
                'value' => $this->nullableString($row['value'] ?? null, 255),
                'ordinal' => $ordinal !== null && $ordinal >= 0 ? (int) $ordinal : 0,
            ];
        }

        return ['rows' => $prepared, 'errors' => $errors];
    }

    protected function validateCrossReferences(array $products, array $variants, array $specifications, array &$errors): void
    {
        $variantByProduct = collect($variants)->groupBy(fn ($variant) => $this->key($variant['product_sku']));
        $variantSkus = collect($variants)->pluck('variant_sku')->map(fn ($sku) => $this->key($sku));

        foreach ($products as $product) {
            $rows = $variantByProduct->get($this->key($product['sku']), collect());
            if ($product['type'] === ProductConst::VARIANT && $rows->isEmpty()) {
                $errors[] = $this->error($product['row'], 'type', __('admin/product.import.errors.variant_rows_required'));
            }
            if ($product['type'] === ProductConst::SINGLE && $rows->isNotEmpty()) {
                $errors[] = $this->error($product['row'], 'type', __('admin/product.import.errors.single_has_variants'));
            }

            $combinations = [];
            foreach ($rows as $variant) {
                $signature = collect($variant['attribute_value_ids'])->sort()->implode('-');
                if ($signature === '') {
                    $errors[] = $this->error($variant['row'], 'attribute_values', __('admin/product.import.errors.attributes_required'));
                } elseif (isset($combinations[$signature])) {
                    $errors[] = $this->error($variant['row'], 'attribute_values', __('admin/product.import.errors.duplicate_variant'));
                } else {
                    $combinations[$signature] = true;
                }
            }
        }

        $existingProductSkus = Product::query()
            ->whereIn('sku', collect($products)->pluck('sku')->all())
            ->get(['id', 'sku', 'name'])
            ->keyBy(fn ($product) => $this->key($product->sku));
        $existingVariantSkus = ProductVariant::query()
            ->whereIn('sku', $variantSkus->all())
            ->get(['id', 'sku', 'product_id'])
            ->keyBy(fn ($variant) => $this->key($variant->sku));

        foreach ($products as $product) {
            $existing = $existingProductSkus->get($this->key($product['sku']));
            $nameCollision = Product::query()
                ->where('name', $product['name'])
                ->when($existing, fn ($query) => $query->where('id', '!=', $existing->id))
                ->exists();
            if ($nameCollision) {
                $errors[] = $this->error($product['row'], 'product_name', __('admin/product.import.errors.name_exists'));
            }
        }

        foreach ($variants as $variant) {
            $existing = $existingVariantSkus->get($this->key($variant['variant_sku']));
            $product = $existingProductSkus->get($this->key($variant['product_sku']));
            if ($existing && (! $product || (string) $existing->product_id !== (string) $product->id)) {
                $errors[] = $this->error($variant['row'], 'variant_sku', __('admin/product.import.errors.variant_sku_exists'));
            }
        }
    }

    protected function persist(array $products, array $variants, array $specifications, array $lookups): array
    {
        $productModels = [];
        $result = [
            'products_created' => 0,
            'products_updated' => 0,
            'variants_created' => 0,
            'variants_updated' => 0,
            'specifications_created' => 0,
            'specifications_updated' => 0,
        ];

        foreach ($products as $data) {
            $model = Product::query()->where('sku', $data['sku'])->first();
            $attributes = [
                'branch_id' => $data['branch_id'],
                'name' => $data['name'],
                'sku' => $data['sku'],
                'stock' => $data['stock'],
                'short_descriptions' => $data['short_descriptions'],
                'descriptions' => $data['descriptions'],
                'thumbnail' => $data['thumbnail'],
                'type' => $data['type'],
                'price' => $data['price'],
                'sale_price' => $data['sale_price'],
                'sale_price_start_at' => $data['sale_price_start_at'],
                'sale_price_end_at' => $data['sale_price_end_at'],
                'is_sale' => $data['sale_price'] !== null,
                'is_featured' => $data['is_featured'],
                'is_trending' => $data['is_trending'],
                'is_active' => $data['is_active'],
            ];
            if ($model) {
                $model->update($attributes);
            } else {
                $model = Product::create(array_merge($attributes, [
                    'slug' => $this->uniqueSlug($data['name']),
                ]));
            }
            $model->categories()->sync($data['category_ids']);

            $tagIds = collect($data['tags'])
                ->filter()
                ->map(fn ($name) => Tag::firstOrCreate(
                    ['slug' => Str::slug(Str::ascii($name))],
                    ['name' => $name]
                )->id)
                ->values()
                ->all();
            $model->tags()->sync($tagIds);
            $productModels[$this->key($data['sku'])] = $model;

            $result[$model->wasRecentlyCreated ? 'products_created' : 'products_updated']++;
        }

        foreach ($variants as $data) {
            $product = $productModels[$this->key($data['product_sku'])];
            $model = ProductVariant::query()->where('sku', $data['variant_sku'])->first();
            $attributes = [
                'product_id' => $product->id,
                'sku' => $data['variant_sku'],
                'price' => $data['price'],
                'sale_price' => $data['sale_price'],
                'stock' => $data['stock'],
                'thumbnail' => $product->thumbnail ?: 'products/placeholder.webp',
                'is_active' => $data['is_active'],
            ];
            $model ? $model->update($attributes) : $model = ProductVariant::create($attributes);
            $model->attributeValues()->sync($data['attribute_value_ids']);

            $result[$model->wasRecentlyCreated ? 'variants_created' : 'variants_updated']++;
        }

        foreach ($specifications as $data) {
            $product = $productModels[$this->key($data['product_sku'])];
            $model = ProductSpecification::query()
                ->where('product_id', $product->id)
                ->where('group', $data['group'])
                ->where('name', $data['name'])
                ->first();
            $attributes = [
                'product_id' => $product->id,
                'group' => $data['group'],
                'name' => $data['name'],
                'value' => $data['value'],
                'ordinal' => $data['ordinal'],
            ];
            $model ? $model->update($attributes) : $model = ProductSpecification::create($attributes);
            $result[$model->wasRecentlyCreated ? 'specifications_created' : 'specifications_updated']++;
        }

        foreach ($products as $data) {
            $product = $productModels[$this->key($data['sku'])];

            if ($data['type'] === ProductConst::SINGLE) {
                $product->variants()->get()->each(function (ProductVariant $variant) {
                    $variant->attributeValues()->detach();
                    $variant->delete();
                });
                continue;
            }

            $variantModels = $product->variants()->get();
            $prices = $variantModels->pluck('price')->filter(fn ($price) => $price !== null);
            $salePrices = $variantModels->pluck('sale_price')->filter(fn ($price) => $price !== null);
            $stock = $variantModels->sum('stock');
            if ($prices->isNotEmpty()) {
                $product->update([
                    'price' => $prices->min(),
                    'sale_price' => $salePrices->count() === $prices->count() ? $salePrices->min() : null,
                    'is_sale' => $salePrices->count() === $prices->count() && $salePrices->isNotEmpty(),
                    'stock' => $stock,
                ]);
            } else {
                $product->update(['stock' => $stock]);
            }
        }

        return $result;
    }

    protected function uniqueSlug(string $name): string
    {
        $base = Str::slug(Str::ascii($name)) ?: 'product';
        $slug = $base;
        $suffix = 2;

        while (Product::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $suffix++;
        }

        return $slug;
    }

    protected function lookups(): array
    {
        return [
            'branches' => Branch::query()->where('is_active', true)->get()->mapWithKeys(fn ($item) => [
                $this->key($item->name) => $item,
                $this->key($item->slug) => $item,
            ]),
            'categories' => Category::query()->where('is_active', true)->get()->mapWithKeys(fn ($item) => [
                $this->key($item->name) => $item,
                $this->key($item->slug) => $item,
            ]),
            'attributes' => AttributeValue::query()
                ->with('attribute')
                ->where('is_active', true)
                ->get()
                ->mapWithKeys(fn ($item) => [$this->attributeKey($item->attribute?->name, $item->value) => $item]),
        ];
    }

    protected function parseAttributeValues(?string $value, array $lookups, int $rowNumber, array &$errors): array
    {
        $ids = [];
        $attributeIds = [];
        $parts = preg_split('/\s*;\s*/', trim((string) $value), -1, PREG_SPLIT_NO_EMPTY);

        foreach ($parts as $part) {
            [$attribute, $attributeValue] = array_pad(explode('=', $part, 2), 2, null);
            $attribute = trim((string) $attribute);
            $attributeValue = trim((string) $attributeValue);
            $model = $lookups['attributes'][$this->attributeKey($attribute, $attributeValue)] ?? null;

            if (! $model) {
                $errors[] = $this->error($rowNumber, 'attribute_values', __('admin/product.import.errors.attribute_lookup', ['value' => $part]));
                continue;
            }

            if (isset($attributeIds[$model->attribute_id])) {
                $errors[] = $this->error($rowNumber, 'attribute_values', __('admin/product.import.errors.one_value_per_attribute'));
                continue;
            }

            $attributeIds[$model->attribute_id] = true;
            $ids[] = $model->id;
        }

        return array_values(array_unique($ids));
    }

    protected function readWorkbook(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        if ($extension !== 'xlsx') {
            return ['Products' => $this->readCsv($file->getRealPath())];
        }

        $zip = new ZipArchive();
        if ($zip->open($file->getRealPath()) !== true) {
            throw new ProductImportException([__('admin/product.import.errors.invalid_workbook')]);
        }

        $sharedStrings = $this->sharedStrings($zip->getFromName('xl/sharedStrings.xml') ?: null);
        $workbookXml = $zip->getFromName('xl/workbook.xml');
        $relsXml = $zip->getFromName('xl/_rels/workbook.xml.rels');
        if (! $workbookXml || ! $relsXml) {
            $zip->close();
            throw new ProductImportException([__('admin/product.import.errors.invalid_workbook')]);
        }

        $workbook = $this->xml($workbookXml);
        $rels = $this->xml($relsXml);
        $relMap = [];
        foreach ($rels->getElementsByTagName('Relationship') as $relation) {
            $relMap[$relation->getAttribute('Id')] = ltrim($relation->getAttribute('Target'), '/');
        }

        $sheets = [];
        foreach ($workbook->getElementsByTagName('sheet') as $sheet) {
            $name = $sheet->getAttribute('name');
            $relationId = $sheet->getAttributeNS('http://schemas.openxmlformats.org/officeDocument/2006/relationships', 'id');
            $target = $relMap[$relationId] ?? null;
            if (! $target) {
                continue;
            }
            $target = str_starts_with($target, 'xl/') ? $target : 'xl/' . $target;
            $xml = $zip->getFromName($target);
            if ($xml !== false) {
                $sheets[$name] = $this->parseSheet($xml, $sharedStrings);
            }
        }

        $zip->close();

        return $sheets;
    }

    protected function parseSheet(string $content, array $sharedStrings): array
    {
        $document = $this->xml($content);
        $rows = [];
        foreach ($document->getElementsByTagName('row') as $row) {
            $cells = [];
            foreach ($row->getElementsByTagName('c') as $cell) {
                $ref = $cell->getAttribute('r');
                preg_match('/^([A-Z]+)/', $ref, $match);
                $column = $this->columnNumber($match[1] ?? 'A');
                $type = $cell->getAttribute('t');
                $value = '';
                if ($type === 'inlineStr') {
                    foreach ($cell->getElementsByTagName('t') as $text) {
                        $value .= $text->textContent;
                    }
                } else {
                    $valueNode = null;
                    foreach ($cell->childNodes as $child) {
                        if ($child->localName === 'v') {
                            $valueNode = $child;
                            break;
                        }
                    }
                    $value = $valueNode?->textContent ?? '';
                    if ($type === 's') {
                        $value = $sharedStrings[(int) $value] ?? '';
                    }
                }
                $cells[$column] = trim((string) $value);
            }
            if ($cells !== []) {
                $rows[] = $cells;
            }
        }

        return $rows;
    }

    protected function sharedStrings(?string $content): array
    {
        if (! $content) {
            return [];
        }

        $document = $this->xml($content);
        $strings = [];
        foreach ($document->getElementsByTagName('si') as $item) {
            $value = '';
            foreach ($item->getElementsByTagName('t') as $text) {
                $value .= $text->textContent;
            }
            $strings[] = $value;
        }

        return $strings;
    }

    protected function readCsv(string $path): array
    {
        $handle = fopen($path, 'rb');
        if (! $handle) {
            throw new ProductImportException([__('admin/product.import.errors.invalid_workbook')]);
        }

        $firstLine = fgets($handle) ?: '';
        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';
        rewind($handle);
        $rows = [];
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (count(array_filter($row, fn ($value) => trim((string) $value) !== '')) > 0) {
                $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $row[0]);
                $rows[] = array_map(fn ($value) => trim((string) $value), $row);
            }
        }
        fclose($handle);

        return $rows;
    }

    protected function rowsFromSheet(array $sheets, string $name): array
    {
        $rows = $sheets[$name] ?? [];
        if ($rows === []) {
            return [];
        }

        $headers = array_map(fn ($value) => $this->header((string) $value), array_values($rows[0]));
        $headers = array_map(fn ($header) => $this->alias($header), $headers);
        $required = match ($name) {
            'Products' => self::PRODUCT_HEADERS,
            'Variants' => self::VARIANT_HEADERS,
            'Specifications' => self::SPECIFICATION_HEADERS,
            default => [],
        };
        $missing = array_diff($required, $headers);
        if ($missing !== []) {
            throw new ProductImportException([
                __('admin/product.import.errors.missing_headers', ['sheet' => $name, 'headers' => implode(', ', $missing)]),
            ]);
        }

        $result = [];
        foreach (array_slice($rows, 1) as $index => $values) {
            $row = [];
            foreach ($headers as $column => $header) {
                $row[$header] = $values[$column] ?? '';
            }
            if (count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0) {
                continue;
            }
            $result[$index + 2] = $row;
        }

        return $result;
    }

    protected function normalizeRow(array $row): array
    {
        return collect($row)->mapWithKeys(fn ($value, $key) => [$this->alias($this->header((string) $key)) => trim((string) $value)])->all();
    }

    protected function alias(string $header): string
    {
        return [
            'sku' => 'product_sku',
            'product_code' => 'product_sku',
            'name' => 'product_name',
            'brand_name' => 'brand',
            'category' => 'categories',
            'category_names' => 'categories',
            'short_descriptions' => 'short_description',
            'descriptions' => 'description',
            'sale_price_start_at' => 'sale_start_at',
            'sale_price_end_at' => 'sale_end_at',
            'attributes' => 'attribute_values',
            'variant_code' => 'variant_sku',
        ][$header] ?? $header;
    }

    protected function header(string $header): string
    {
        return Str::snake(trim($header));
    }

    protected function productType(mixed $value): ?int
    {
        return match (Str::lower(trim((string) $value))) {
            'single', 'simple', '0', 'đơn', 'don' => ProductConst::SINGLE,
            'variant', 'variable', '1', 'biến thể', 'bien the' => ProductConst::VARIANT,
            default => null,
        };
    }

    protected function lookup($lookup, mixed $value): mixed
    {
        $value = trim((string) $value);

        return $value === '' ? null : ($lookup[$this->key($value)] ?? null);
    }

    protected function splitList(mixed $value): array
    {
        return collect(preg_split('/\s*[|]\s*/', trim((string) $value), -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->unique(fn ($item) => $this->key($item))
            ->values()
            ->all();
    }

    protected function number(mixed $value): ?float
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $value = str_replace([',', ' '], ['', ''], $value);

        return is_numeric($value) ? (float) $value : null;
    }

    protected function date(mixed $value): ?Carbon
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    protected function boolean(mixed $value, bool $default): bool
    {
        $value = trim(Str::lower((string) $value));
        if ($value === '') {
            return $default;
        }

        return in_array($value, ['1', 'true', 'yes', 'y', 'on', 'active', 'đang bán', 'dang ban'], true);
    }

    protected function nullableString(mixed $value, int $max): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : Str::limit($value, $max, '');
    }

    protected function key(mixed $value): string
    {
        return Str::lower(trim((string) $value));
    }

    protected function attributeKey(mixed $attribute, mixed $value): string
    {
        return $this->key($attribute) . '|' . $this->key($value);
    }

    protected function error(int $row, string $field, string $message): string
    {
        return __('admin/product.import.errors.row', ['row' => $row, 'field' => $field, 'message' => $message]);
    }

    protected function columnNumber(string $letters): int
    {
        $number = 0;
        foreach (str_split($letters) as $letter) {
            $number = $number * 26 + ord($letter) - 64;
        }

        return $number - 1;
    }

    protected function xml(string $content): \DOMDocument
    {
        $document = new \DOMDocument();
        if (! $document->loadXML($content, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
            throw new ProductImportException([__('admin/product.import.errors.invalid_workbook')]);
        }

        return $document;
    }
}
