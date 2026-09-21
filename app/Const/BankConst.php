<?php

namespace App\Const;

class BankConst
{
    public const BANKS = [
        'VCB' => [
            'name' => 'Ngân hàng TMCP Ngoại thương Việt Nam',
            'short_name' => 'Vietcombank',
            'bin' => '970436',
        ],
        'TCB' => [
            'name' => 'Ngân hàng TMCP Kỹ thương Việt Nam',
            'short_name' => 'Techcombank',
            'bin' => '970407',
        ],
        'BIDV' => [
            'name' => 'Ngân hàng TMCP Đầu tư và Phát triển Việt Nam',
            'short_name' => 'BIDV',
            'bin' => '970418',
        ],
        'VTB' => [
            'name' => 'Ngân hàng TMCP Công thương Việt Nam',
            'short_name' => 'VietinBank',
            'bin' => '970415',
        ],
        'ACB' => [
            'name' => 'Ngân hàng TMCP Á Châu',
            'short_name' => 'ACB',
            'bin' => '970416',
        ],
        'MB' => [
            'name' => 'Ngân hàng TMCP Quân đội',
            'short_name' => 'MB Bank',
            'bin' => '970422',
        ],
        'TPB' => [
            'name' => 'Ngân hàng TMCP Tiên Phong',
            'short_name' => 'TPBank',
            'bin' => '970423',
        ],
        'VPB' => [
            'name' => 'Ngân hàng TMCP Việt Nam Thịnh Vượng',
            'short_name' => 'VPBank',
            'bin' => '970432',
        ],
        'STB' => [
            'name' => 'Ngân hàng TMCP Sài Gòn Thương Tín',
            'short_name' => 'Sacombank',
            'bin' => '970403',
        ],
        'HDB' => [
            'name' => 'Ngân hàng TMCP Phát triển TP.HCM',
            'short_name' => 'HDBank',
            'bin' => '970437',
        ],
        'OCB' => [
            'name' => 'Ngân hàng TMCP Phương Đông',
            'short_name' => 'OCB',
            'bin' => '970448',
        ],
        'SHB' => [
            'name' => 'Ngân hàng TMCP Sài Gòn - Hà Nội',
            'short_name' => 'SHB',
            'bin' => '970443',
        ],
        'VIB' => [
            'name' => 'Ngân hàng TMCP Quốc tế Việt Nam',
            'short_name' => 'VIB',
            'bin' => '970441',
        ],
        'MSB' => [
            'name' => 'Ngân hàng TMCP Hàng Hải Việt Nam',
            'short_name' => 'MSB',
            'bin' => '970426',
        ],
        'EIB' => [
            'name' => 'Ngân hàng TMCP Xuất Nhập khẩu Việt Nam',
            'short_name' => 'Eximbank',
            'bin' => '970431',
        ],
        'SCB' => [
            'name' => 'Ngân hàng TMCP Sài Gòn',
            'short_name' => 'SCB',
            'bin' => '970429',
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

    public static function getShortName(string $code): ?string
    {
        return self::BANKS[$code]['short_name'] ?? null;
    }

    public static function getBin(string $code): ?string
    {
        return self::BANKS[$code]['bin'] ?? null;
    }

    public static function vietQrUrl(string $bankCode, string $accountNumber, float $amount, string $note, ?string $accountName = null, string $template = 'compact2'): ?string
    {
        $bin = self::getBin($bankCode);

        if (! $bin || ! $accountNumber) {
            return null;
        }

        $params = http_build_query(array_filter([
            'amount' => (int) round($amount),
            'addInfo' => $note,
            'accountName' => $accountName,
        ]));

        return "https://img.vietqr.io/image/{$bin}-{$accountNumber}-{$template}.png?{$params}";
    }
}
