<?php

namespace App\Services\Admin;

use App\Models\Coupon;
use App\Repositories\CouponRepository;
use App\Services\BaseCrudService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CouponService extends BaseCrudService
{
    protected function getRepository(): CouponRepository
    {
        if (empty($this->repository)) {
            $this->repository = app()->make(CouponRepository::class);
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

        if (! empty($params['code'])) {
            $whereLikes['code'] = $params['code'];
        }

        if (! empty($params['discount_type'])) {
            $wheres['discount_type'] = (int) $params['discount_type'];
        }

        if (isset($params['is_active']) && $params['is_active'] !== '' && $params['is_active'] !== null) {
            $wheres['is_active'] = (int) $params['is_active'];
        }

        if (! empty($params['keyword'])) {
            $orWheres[] = ['code', 'like', '%' . $params['keyword'] . '%'];
            $orWheres[] = ['title', 'like', '%' . $params['keyword'] . '%'];
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

    public function prepareConfirmData(array $validated, $id = null): array
    {
        $data = array_merge([
            'code' => null,
            'title' => null,
            'description' => null,
            'discount_type' => null,
            'discount_value' => null,
            'usage_limit' => 0,
            'is_active' => 1,
            'start_date' => null,
            'end_date' => null,
            'min_order_value' => 0,
            'max_discount_value' => null,
            'valid_categories' => [],
        ], $validated);

        $data['id'] = $id;
        $data['code'] = mb_strtoupper(trim((string) $data['code']));
        $data['valid_categories'] = array_values(array_filter($data['valid_categories'] ?? []));

        return $data;
    }

    public function create(array $params = []): Coupon
    {
        return DB::transaction(function () use ($params) {
            $coupon = parent::create(array_merge(Arr::except($params, $this->restrictionKeys()), [
                'usage_count' => 0,
                'is_expired' => false,
            ]));

            $coupon->restriction()->create($this->restrictionAttributes($params));

            return $coupon;
        });
    }

    public function update(int|string $id, array $params = []): Coupon
    {
        return DB::transaction(function () use ($id, $params) {
            $coupon = parent::update($id, Arr::except($params, $this->restrictionKeys()));

            $coupon->restriction()->updateOrCreate(
                ['coupon_id' => $coupon->id],
                $this->restrictionAttributes($params)
            );

            return $coupon->refresh();
        });
    }

    public function delete($id)
    {
        try {
            $coupon = $this->find($id);

            if (! $coupon) {
                return [
                    'status' => false,
                    'message' => __('admin/coupon.messages.not_found'),
                ];
            }

            if ($coupon->usage_count > 0) {
                return [
                    'status' => false,
                    'message' => __('admin/coupon.messages.already_used'),
                ];
            }

            DB::transaction(function () use ($coupon, $id) {
                $coupon->restriction()->delete();
                parent::delete($id);
            });

            return [
                'status' => true,
                'message' => __('admin/coupon.messages.deleted'),
            ];
        } catch (\Throwable $th) {
            Log::error(__METHOD__, ['message' => $th->getMessage(), 'id' => $id]);

            throw $th;
        }
    }

    public function restore($id)
    {
        return $this->getRepository()->restore($id);
    }

    public function forceDelete($id)
    {
        $coupon = $this->getRepository()->findWithTrashed($id);

        if (! $coupon) {
            return false;
        }

        return DB::transaction(function () use ($coupon, $id) {
            $coupon->restriction()->forceDelete();
            $coupon->users()->detach();

            return parent::forceDelete($id);
        });
    }

    protected function restrictionKeys(): array
    {
        return ['id', 'min_order_value', 'max_discount_value', 'valid_categories'];
    }

    protected function restrictionAttributes(array $params): array
    {
        return [
            'min_order_value' => $params['min_order_value'] ?? 0,
            'max_discount_value' => $params['max_discount_value'] ?? null,
            'valid_categories' => ! empty($params['valid_categories']) ? $params['valid_categories'] : null,
            'valid_products' => null,
        ];
    }
}
