<?php

namespace App\Services\Admin;

use App\Const\UserConst;
use App\Models\Province;
use App\Models\User;
use App\Models\Ward;
use App\Repositories\UserAddressRepository;
use App\Repositories\UserRepository;
use App\Services\BaseCrudService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserService extends BaseCrudService
{
    public function __construct(protected UserAddressRepository $userAddressRepository) {}

    protected function getRepository(): UserRepository
    {
        if (empty($this->repository)) {
            $this->repository = app()->make(UserRepository::class);
        }

        return $this->repository;
    }

    protected function buildFilterParams(array $params = []): array
    {
        $wheres = Arr::get($params, 'wheres', []);
        $whereIns = Arr::get($params, 'where_ins', []);
        $whereLikes = Arr::get($params, 'where_likes', []);
        $whereEquals = Arr::get($params, 'where_equals', []);
        $orWheres = Arr::get($params, 'or_wheres', []);
        $sort = Arr::get($params, 'sort', 'id:desc');
        $relates = Arr::get($params, 'relates', []);
        $relatesCount = Arr::get($params, 'relates_count', []);

        if (!empty($params['fullname'])) {
            $whereLikes['fullname'] = $params['fullname'];
        }

        if (!empty($params['email'])) {
            $whereLikes['email'] = $params['email'];
        }

        if (!empty($params['phone_number'])) {
            $whereLikes['phone_number'] = $params['phone_number'];
        }

        if (!empty($params['status'])) {
            $wheres['status'] = $params['status'];
        }

        if (!empty($params['role'])) {
            $wheres['role'] = $params['role'];
        }

        if (!empty($params['keyword'])) {
            $orWheres[] = ['fullname', 'like', '%' . $params['keyword'] . '%'];
            $orWheres[] = ['email', 'like', '%' . $params['keyword'] . '%'];
            $orWheres[] = ['phone_number', 'like', '%' . $params['keyword'] . '%'];
        }

        return [
            'wheres' => $wheres,
            'where_equals' => $whereEquals,
            'or_wheres' => $orWheres,
            'where_likes' => $whereLikes,
            'where_ins' => $whereIns,
            'sort' => $sort,
            'relates' => $relates,
            'relates_count' => $relatesCount,
        ];
    }

    public function create(array $params = []): User
    {
        try {
            DB::beginTransaction();
            $userData = $params;
            unset($userData['user_addresses']);
            $user = parent::create($userData);

            if (!empty($params['user_addresses']) && is_array($params['user_addresses'])) {
                $provinceIds = array_filter(array_column($params['user_addresses'], 'province_id'));
                $wardIds = array_filter(array_column($params['user_addresses'], 'ward_id'));

                $provinces = Province::whereIn('id', $provinceIds)->get()->keyBy('id');
                $wards = Ward::whereIn('id', $wardIds)->get()->keyBy('id');

                $addresses = [];
                $now = now();

                foreach ($params['user_addresses'] as $addressData) {
                    $addressData['user_id'] = $user->id;

                    if (!empty($addressData['province_id']) && isset($provinces[$addressData['province_id']])) {
                        $addressData['province'] = $provinces[$addressData['province_id']]->name;
                    }

                    if (!empty($addressData['ward_id']) && isset($wards[$addressData['ward_id']])) {
                        $addressData['ward'] = $wards[$addressData['ward_id']]->name;
                    }

                    $addressData['created_at'] = $now;
                    $addressData['updated_at'] = $now;

                    $addresses[] = $addressData;
                }

                $this->userAddressRepository->insert($addresses);
            }

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage(), ['params' => $params]);
            DB::rollBack();
            throw $e;
        }
    }

    public function update(int|string $id, array $params = []): User
    {
        try {
            DB::beginTransaction();
            $userData = $params;
            unset($userData['user_addresses']);
            $user = parent::update($id, $userData);

            if (!empty($params['user_addresses']) && is_array($params['user_addresses'])) {

                $provinceIds = array_filter(array_column($params['user_addresses'], 'province_id'));
                $wardIds = array_filter(array_column($params['user_addresses'], 'ward_id'));

                $provinces = Province::whereIn('id', $provinceIds)->get()->keyBy('id');
                $wards = Ward::whereIn('id', $wardIds)->get()->keyBy('id');

                $currentAddresses = $user->userAddresses()->get()->keyBy('id');

                $keptIds = [];

                foreach ($params['user_addresses'] as $addressData) {

                    if (!empty($addressData['province_id']) && isset($provinces[$addressData['province_id']])) {
                        $addressData['province'] = $provinces[$addressData['province_id']]->name;
                    }

                    if (!empty($addressData['ward_id']) && isset($wards[$addressData['ward_id']])) {
                        $addressData['ward'] = $wards[$addressData['ward_id']]->name;
                    }

                    if (!empty($addressData['id']) && isset($currentAddresses[$addressData['id']])) {

                        $address = $currentAddresses[$addressData['id']];
                        $address->update($addressData);

                        $keptIds[] = $address->id;
                    } else {
                        $address = $user->userAddresses()->create($addressData);
                        $keptIds[] = $address->id;
                    }
                }

                // DELETE cái bị remove
                $user->userAddresses()
                    ->whereNotIn('id', $keptIds)
                    ->delete();
            }

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage(), ['params' => $params]);
            DB::rollBack();
            throw $e;
        }
    }

    public function delete($id): bool
    {
        try {
            $user = $this->find($id);
            $user->update(['status' => UserConst::STATUS_INACTIVE]);
            $user = parent::delete($id);

            return $user;
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage(), ['id' => $id]);
            throw $e;
        }
    }

    public function restore(int|string $id): bool
    {
        try {
            $user = $this->getRepository()->restore($id);
            return $user;
        } catch (\Exception $e) {
            Log::error('Error restoring user: ' . $e->getMessage(), ['id' => $id]);
            throw $e;
        }
    }

    public function forceDelete(int|string $id): bool
    {
        try {
            DB::beginTransaction();
            $user = parent::delete($id);

            if ($user) {
                $this->userAddressRepository->filter(['user_id' => $id])->delete();
            }

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage(), ['id' => $id]);
            DB::rollBack();
            throw $e;
        }
    }

    public function mapAddressName(array $addresses = []): array
    {
        $addresses = collect($addresses);

        $provinces = Province::select('id', 'name')->get()->keyBy('id');

        $wardIds = $addresses->pluck('ward_id')->filter();
        $wards = Ward::whereIn('id', $wardIds)->get()->keyBy('id');

        return $addresses->map(function ($addr) use ($provinces, $wards) {
            $provinceId = $addr['province_id'] ?? null;
            $wardId = $addr['ward_id'] ?? null;

            $addr['province'] = $provinceId && isset($provinces[$provinceId])
                ? $provinces[$provinceId]->name
                : '-';

            $addr['ward'] = $wardId && isset($wards[$wardId])
                ? $wards[$wardId]->name
                : '-';

            return $addr;
        })->toArray();
    }
}
