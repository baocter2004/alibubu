<?php

if (! function_exists('format_price')) {
    function format_price(int|float|string|null $amount, string $suffix = 'đ'): string
    {
        return number_format((float) $amount, 0, ',', '.') . $suffix;
    }
}
