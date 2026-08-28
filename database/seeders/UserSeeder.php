<?php

namespace Database\Seeders;

use App\Const\UserConst;
use App\Models\Province;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provinces = Province::with('wards')->get();

        foreach ($this->data() as $index => $item) {
            $user = User::updateOrCreate(
                ['email' => $item['email']],
                [
                    'fullname' => $item['fullname'],
                    'password' => 'password',
                    'phone_number' => $item['phone_number'],
                    'role' => $item['role'],
                    'status' => UserConst::STATUS_ACTIVE,
                    'gender' => $item['gender'],
                    'email_verified_at' => now(),
                    'loyalty_points' => $item['loyalty_points'],
                ]
            );

            if ($provinces->isEmpty() || $user->userAddresses()->exists()) {
                continue;
            }

            $province = $provinces[$index % $provinces->count()];
            $ward = $province->wards->first();

            UserAddress::create([
                'user_id' => $user->id,
                'province_id' => $province->id,
                'ward_id' => $ward?->id,
                'province' => $province->name,
                'ward' => $ward?->name,
                'address' => $item['address'],
                'phone_number' => $item['phone_number'],
                'fullname' => $item['fullname'],
                'is_default' => true,
            ]);
        }
    }

    protected function data(): array
    {
        return [
            [
                'fullname' => 'Nguyễn Văn An',
                'email' => 'user@alibubu.test',
                'phone_number' => '0901000001',
                'role' => UserConst::ROLE_USER,
                'gender' => UserConst::MALE,
                'loyalty_points' => 1200,
                'address' => 'Số 12, ngõ 45 Trần Duy Hưng',
            ],
            [
                'fullname' => 'Trần Thị Bình',
                'email' => 'binh@alibubu.test',
                'phone_number' => '0901000002',
                'role' => UserConst::ROLE_USER,
                'gender' => UserConst::FEMALE,
                'loyalty_points' => 450,
                'address' => '88 Nguyễn Huệ',
            ],
            [
                'fullname' => 'Lê Minh Cường',
                'email' => 'cuong@alibubu.test',
                'phone_number' => '0901000003',
                'role' => UserConst::ROLE_EMPLOYEE,
                'gender' => UserConst::MALE,
                'loyalty_points' => 0,
                'address' => '25 Bạch Đằng',
            ],
            [
                'fullname' => 'Phạm Thu Dung',
                'email' => 'dung@alibubu.test',
                'phone_number' => '0901000004',
                'role' => UserConst::ROLE_USER,
                'gender' => UserConst::FEMALE,
                'loyalty_points' => 3100,
                'address' => '17 Lê Lợi',
            ],
            [
                'fullname' => 'Đỗ Quang Huy',
                'email' => 'huy@alibubu.test',
                'phone_number' => '0901000005',
                'role' => UserConst::ROLE_ADMIN,
                'gender' => UserConst::MALE,
                'loyalty_points' => 780,
                'address' => '30/4 Nguyễn Văn Cừ',
            ],
        ];
    }
}
