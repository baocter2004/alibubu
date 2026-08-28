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
            return DB::transaction(function () use ($params) {
                $user = parent::create(Arr::except($params, ['user_addresses', 'id']));
                $addresses = $this->normalizeAddresses($params['user_addresses'] ?? [], $user->id);

                if ($addresses !== []) {
                    $this->userAddressRepository->insert(
                        array_map(fn ($address) => Arr::except($address, ['id']), $addresses)
                    );
                }

                return $user;
            });
        } catch (\Throwable $th) {
            Log::error(__METHOD__, ['message' => $th->getMessage(), 'params' => $params]);

            throw $th;
        }
    }

    public function update(int|string $id, array $params = []): User
    {
        try {
            return DB::transaction(function () use ($id, $params) {
                $user = parent::update($id, Arr::except($params, ['user_addresses', 'id']));
                $addresses = $this->normalizeAddresses($params['user_addresses'] ?? [], $user->id);

                $currentIds = $user->userAddresses()->pluck('id')->map(fn ($id) => (string) $id)->all();
                $keptIds = [];
                $toInsert = [];

                foreach ($addresses as $address) {
                    if (! empty($address['id']) && in_array((string) $address['id'], $currentIds, true)) {
                        $keptIds[] = (string) $address['id'];
                        $user->userAddresses()
                            ->whereKey($address['id'])
                            ->update(Arr::except($address, ['id', 'user_id', 'created_at']));

                        continue;
                    }

                    $toInsert[] = Arr::except($address, ['id']);
                }

                if ($toInsert !== []) {
                    $this->userAddressRepository->insert($toInsert);
                }

                $idsToDelete = array_diff($currentIds, $keptIds);

                if ($idsToDelete !== []) {
                    $user->userAddresses()->whereIn('id', $idsToDelete)->delete();
                }

                return $user->refresh();
            });
        } catch (\Throwable $th) {
            Log::error(__METHOD__, ['message' => $th->getMessage(), 'id' => $id, 'params' => $params]);

            throw $th;
        }
    }

    protected function normalizeAddresses(mixed $addresses, int|string $userId): array
    {
        if (! is_array($addresses) || $addresses === []) {
            return [];
        }

        $provinces = Province::whereIn('id', array_filter(array_column($addresses, 'province_id')))
            ->pluck('name', 'id');
        $wards = Ward::whereIn('id', array_filter(array_column($addresses, 'ward_id')))
            ->pluck('name', 'id');

        $now = now();
        $normalized = [];

        foreach ($addresses as $address) {
            if (empty($address['address']) && empty($address['fullname'])) {
                continue;
            }

            $normalized[] = [
                'id' => $address['id'] ?? null,
                'user_id' => $userId,
                'province_id' => $address['province_id'] ?? null,
                'ward_id' => $address['ward_id'] ?? null,
                'province' => $provinces[$address['province_id'] ?? null] ?? null,
                'ward' => $wards[$address['ward_id'] ?? null] ?? null,
                'address' => $address['address'] ?? null,
                'phone_number' => $address['phone_number'] ?? null,
                'fullname' => $address['fullname'] ?? null,
                'is_default' => ! empty($address['is_default']),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        return $normalized;
    }

    public function delete($id)
    {
        try {
            $user = $this->find($id);

            if (! $user) {
                return [
                    'status' => false,
                    'message' => __('admin/user.messages.not_found'),
                ];
            }

            DB::transaction(function () use ($user, $id) {
                $user->update(['status' => UserConst::STATUS_INACTIVE]);
                parent::delete($id);
            });

            return [
                'status' => true,
                'message' => __('admin/user.messages.deleted'),
            ];
        } catch (\Throwable $th) {
            Log::error(__METHOD__, ['message' => $th->getMessage(), 'id' => $id]);

            throw $th;
        }
    }

    public function restore($id)
    {
        try {
            $restored = $this->getRepository()->restore($id);

            $this->find($id)?->update(['status' => UserConst::STATUS_ACTIVE]);

            return $restored;
        } catch (\Throwable $th) {
            Log::error(__METHOD__, ['message' => $th->getMessage(), 'id' => $id]);

            throw $th;
        }
    }

    public function forceDelete($id)
    {
        try {
            $user = $this->getRepository()->findWithTrashed($id);

            if (! $user) {
                return false;
            }

            return DB::transaction(function () use ($user, $id) {
                $user->userAddresses()->delete();

                return parent::forceDelete($id);
            });
        } catch (\Throwable $th) {
            Log::error(__METHOD__, ['message' => $th->getMessage(), 'id' => $id]);

            throw $th;
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
