<?php

namespace App\Const;

class BankConst
{
    public const BANKS = [
        'VCB' => [
            'name' => 'Ngân hàng TMCP Ngoại thương Việt Nam',
            'short_name' => 'Vietcombank',
        ],
        'TCB' => [
            'name' => 'Ngân hàng TMCP Kỹ thương Việt Nam',
            'short_name' => 'Techcombank',
        ],
        'BIDV' => [
            'name' => 'Ngân hàng TMCP Đầu tư và Phát triển Việt Nam',
            'short_name' => 'BIDV',
        ],
        'VTB' => [
            'name' => 'Ngân hàng TMCP Công thương Việt Nam',
            'short_name' => 'VietinBank',
        ],
        'ACB' => [
            'name' => 'Ngân hàng TMCP Á Châu',
            'short_name' => 'ACB',
        ],
        'MB' => [
            'name' => 'Ngân hàng TMCP Quân đội',
            'short_name' => 'MB Bank',
        ],
        'TPB' => [
            'name' => 'Ngân hàng TMCP Tiên Phong',
            'short_name' => 'TPBank',
        ],
        'VPB' => [
            'name' => 'Ngân hàng TMCP Việt Nam Thịnh Vượng',
            'short_name' => 'VPBank',
        ],
        'STB' => [
            'name' => 'Ngân hàng TMCP Sài Gòn Thương Tín',
            'short_name' => 'Sacombank',
        ],
        'HDB' => [
            'name' => 'Ngân hàng TMCP Phát triển TP.HCM',
            'short_name' => 'HDBank',
        ],
        'OCB' => [
            'name' => 'Ngân hàng TMCP Phương Đông',
            'short_name' => 'OCB',
        ],
        'SHB' => [
            'name' => 'Ngân hàng TMCP Sài Gòn - Hà Nội',
            'short_name' => 'SHB',
        ],
        'VIB' => [
            'name' => 'Ngân hàng TMCP Quốc tế Việt Nam',
            'short_name' => 'VIB',
        ],
        'MSB' => [
            'name' => 'Ngân hàng TMCP Hàng Hải Việt Nam',
            'short_name' => 'MSB',
        ],
        'EIB' => [
            'name' => 'Ngân hàng TMCP Xuất Nhập khẩu Việt Nam',
            'short_name' => 'Eximbank',
        ],
        'SCB' => [
            'name' => 'Ngân hàng TMCP Sài Gòn',
            'short_name' => 'SCB',
        ],
    ];

    public static function getOptions(): array
    {
        return collect(self::BANKS)->mapWithKeys(function ($bank, $code) {
            return [$code => $bank['short_name']];
        })->toArray();
    }

    public static function getName(string $code): ?string
    {
        return self::BANKS[$code]['name'] ?? null;
    }
}
