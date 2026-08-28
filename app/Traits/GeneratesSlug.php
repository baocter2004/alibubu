<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait GeneratesSlug
{
    protected function generateSlug(string $source, string $table, int|string|null $ignoreId = null, string $column = 'slug'): string
    {
        $base = Str::slug(Str::ascii($source)) ?: Str::lower(Str::random(8));
        $slug = $base;
        $suffix = 1;

        while ($this->slugExists($slug, $table, $ignoreId, $column)) {
            $suffix++;
            $slug = $base . '-' . $suffix;
        }

        return $slug;
    }

    protected function slugExists(string $slug, string $table, int|string|null $ignoreId, string $column): bool
    {
        return \Illuminate\Support\Facades\DB::table($table)
            ->where($column, $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists();
    }
}
