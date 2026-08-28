<?php

namespace Database\Seeders;

use App\Models\Province;
use App\Models\Ward;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProvinceWardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->data() as $item) {
            $province = Province::updateOrCreate(
                ['code' => $item['code']],
                [
                    'name' => $item['name'],
                    'division_type' => $item['division_type'],
                    'codename' => Str::snake(Str::ascii($item['name'])),
                    'phone_code' => $item['phone_code'],
                ]
            );

            foreach ($item['wards'] as $index => $wardName) {
                Ward::updateOrCreate(
                    ['code' => $item['code'] * 1000 + $index],
                    [
                        'name' => $wardName,
                        'division_type' => 'phường',
                        'codename' => Str::snake(Str::ascii($wardName)),
                        'province_id' => $province->id,
                    ]
                );
            }
        }
    }

    protected function data(): array
    {
        return [
            [
                'code' => 1,
                'name' => 'Thành phố Hà Nội',
                'division_type' => 'thành phố trung ương',
                'phone_code' => 24,
                'wards' => ['Phường Ba Đình', 'Phường Hoàn Kiếm', 'Phường Đống Đa', 'Phường Cầu Giấy', 'Phường Thanh Xuân'],
            ],
            [
                'code' => 79,
                'name' => 'Thành phố Hồ Chí Minh',
                'division_type' => 'thành phố trung ương',
                'phone_code' => 28,
                'wards' => ['Phường Bến Nghé', 'Phường Bến Thành', 'Phường Tân Định', 'Phường Thảo Điền', 'Phường An Khánh'],
            ],
            [
                'code' => 48,
                'name' => 'Thành phố Đà Nẵng',
                'division_type' => 'thành phố trung ương',
                'phone_code' => 236,
                'wards' => ['Phường Hải Châu', 'Phường Thanh Khê', 'Phường Sơn Trà', 'Phường Ngũ Hành Sơn'],
            ],
            [
                'code' => 31,
                'name' => 'Thành phố Hải Phòng',
                'division_type' => 'thành phố trung ương',
                'phone_code' => 225,
                'wards' => ['Phường Hồng Bàng', 'Phường Lê Chân', 'Phường Ngô Quyền'],
            ],
            [
                'code' => 92,
                'name' => 'Thành phố Cần Thơ',
                'division_type' => 'thành phố trung ương',
                'phone_code' => 292,
                'wards' => ['Phường Ninh Kiều', 'Phường Bình Thủy', 'Phường Cái Răng'],
            ],
            [
                'code' => 56,
                'name' => 'Tỉnh Khánh Hòa',
                'division_type' => 'tỉnh',
                'phone_code' => 258,
                'wards' => ['Phường Nha Trang', 'Phường Cam Ranh', 'Phường Ninh Hòa'],
            ],
            [
                'code' => 68,
                'name' => 'Tỉnh Lâm Đồng',
                'division_type' => 'tỉnh',
                'phone_code' => 263,
                'wards' => ['Phường Đà Lạt', 'Phường Bảo Lộc', 'Phường Đức Trọng'],
            ],
        ];
    }
}
