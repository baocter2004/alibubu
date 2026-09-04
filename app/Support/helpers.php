<?php

if (! function_exists('format_price')) {
    function format_price(int|float|string|null $amount, string $suffix = 'đ'): string
    {
        return number_format((float) $amount, 0, ',', '.') . $suffix;
    }
}

if (! function_exists('short_id')) {
    function short_id(int|string|null $id, int $length = 8): string
    {
        $id = (string) $id;

        if ($id === '' || ! str_contains($id, '-')) {
            return $id;
        }

        return substr($id, -$length);
    }
}
