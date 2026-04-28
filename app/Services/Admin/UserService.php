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
    public function __construct(protected UserAddressRepository $userAddressRepository)
    {
        parent::__construct();
    }

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
                $now = now();

                $toUpsert    = []; // existing addresses to batch-update
                $toInsert    = []; // new addresses to batch-insert
                $keptIds     = []; // IDs of existing addresses that should be kept

                foreach ($params['user_addresses'] as $addressData) {
                    $addressData['user_id']    = $user->id;
                    $addressData['updated_at'] = $now;

                    if (!empty($addressData['province_id']) && isset($provinces[$addressData['province_id']])) {
                        $addressData['province'] = $provinces[$addressData['province_id']]->name;
                    }

                    if (!empty($addressData['ward_id']) && isset($wards[$addressData['ward_id']])) {
                        $addressData['ward'] = $wards[$addressData['ward_id']]->name;
                    }

                    if (!empty($addressData['id']) && isset($currentAddresses[$addressData['id']])) {
                        // Existing address — queue for batch upsert
                        $toUpsert[] = $addressData;
                        $keptIds[]  = $addressData['id'];
                    } else {
                        // New address — queue for batch insert
                        unset($addressData['id']);
                        $addressData['created_at'] = $now;
                        $toInsert[] = $addressData;
                    }
                }

                // Batch-update existing addresses (1 query instead of N)
                if (!empty($toUpsert)) {
                    $updateColumns = [
                        'province_id', 'ward_id', 'province', 'ward',
                        'address', 'phone_number', 'fullname', 'is_default', 'updated_at',
                    ];
                    $this->userAddressRepository->upsert($toUpsert, ['id'], $updateColumns);
                }

                // Batch-insert new addresses (1 query)
                if (!empty($toInsert)) {
                    $this->userAddressRepository->insert($toInsert);
                }

                // Remove addresses that were deleted from the submitted list
                $idsToDelete = array_diff(
                    $currentAddresses->keys()->toArray(),
                    $keptIds
                );

                if (!empty($idsToDelete)) {
                    $user->userAddresses()->whereIn('id', $idsToDelete)->delete();
                }
            }

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage(), ['params' => $params]);
            DB::rollBack();
            throw $e;
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $user = $this->find($id);

            if (!$user) {
                throw new \Exception("User not found: {$id}");
            }

            $user->update(['status' => UserConst::STATUS_INACTIVE]);
            parent::delete($id);

            DB::commit();

            return [
                'status'  => true,
                'message' => 'User Deleted successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting user: ' . $e->getMessage(), ['id' => $id]);
            throw $e;
        }
    }

    public function restore($id)
    {
        try {
            $user = $this->getRepository()->restore($id);
            return $user;
        } catch (\Exception $e) {
            Log::error('Error restoring user: ' . $e->getMessage(), ['id' => $id]);
            throw $e;
        }
    }

    public function forceDelete($id)
    {
        try {
            DB::beginTransaction();

            $user = $this->find($id);

            if (!$user) {
                throw new \Exception("User not found: {$id}");
            }

            // Use the relationship instead of filter() which does not map raw keys
            $user->userAddresses()->delete();

            $result = parent::forceDelete($id);

            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error force deleting user: ' . $e->getMessage(), ['id' => $id]);
            throw $e;
        }
    }

    public function mapAddressName(array $addresses = []): array
    {
        $addresses = collect($addresses);

        // Only load provinces/wards that are actually referenced — avoid full-table scan
        $provinceIds = $addresses->pluck('province_id')->filter()->unique();
        $wardIds     = $addresses->pluck('ward_id')->filter()->unique();

        $provinces = Province::select('id', 'name')
            ->whereIn('id', $provinceIds)
            ->get()
            ->keyBy('id');

        $wards = Ward::select('id', 'name')
            ->whereIn('id', $wardIds)
            ->get()
            ->keyBy('id');

        return $addresses->map(function ($addr) use ($provinces, $wards) {
            $provinceId = $addr['province_id'] ?? null;
            $wardId     = $addr['ward_id'] ?? null;

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
