<?php

namespace App\Services\Admin;

use App\Const\GlobalConst;
use App\Models\Branch;
use App\Repositories\BranchRepository;
use App\Services\BaseCrudService;
use App\Traits\GeneratesSlug;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BranchService extends BaseCrudService
{
    use GeneratesSlug;

    protected function getRepository(): BranchRepository
    {
        if (empty($this->repository)) {
            $this->repository = app()->make(BranchRepository::class);
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

        if (! empty($params['name'])) {
            $whereLikes['name'] = $params['name'];
        }

        if (! empty($params['slug'])) {
            $whereLikes['slug'] = $params['slug'];
        }

        if (isset($params['is_active']) && $params['is_active'] !== '' && $params['is_active'] !== null) {
            $wheres['is_active'] = (int) $params['is_active'];
        }

        if (! empty($params['keyword'])) {
            $orWheres[] = ['name', 'like', '%' . $params['keyword'] . '%'];
            $orWheres[] = ['slug', 'like', '%' . $params['keyword'] . '%'];
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

    public function prepareConfirmData(array $validated, $id = null, ?array $oldSessionData = null): array
    {
        $data = array_merge([
            'name' => null,
            'slug' => null,
            'logo' => null,
            'is_active' => GlobalConst::IS_ACTIVE,
        ], $validated);

        $data['id'] = $id;
        $data['slug'] = $this->generateSlug($data['name'], 'branches', $id);

        if (! empty($validated['logo']) && $validated['logo'] instanceof UploadedFile) {
            if (! empty($oldSessionData['logo']) && $oldSessionData['logo'] !== ($oldSessionData['persisted_logo'] ?? null)) {
                Storage::disk('public')->delete($oldSessionData['logo']);
            }

            $data['logo'] = $validated['logo']->store('branches', 'public');
        } elseif (! empty($oldSessionData['logo'])) {
            $data['logo'] = $oldSessionData['logo'];
        }

        if (! empty($id)) {
            $branch = $this->find($id);
            $data['persisted_logo'] = $branch?->logo;

            if (empty($data['logo'])) {
                $data['logo'] = $branch?->logo;
            }
        }

        return $data;
    }

    public function create(array $params = []): Branch
    {
        $logoPath = Arr::get($params, 'logo');

        try {
            return parent::create(Arr::except($params, ['id', 'persisted_logo']));
        } catch (\Throwable $th) {
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }

            Log::error(__METHOD__, ['message' => $th->getMessage(), 'params' => $params]);

            throw $th;
        }
    }

    public function update(int|string $id, array $params = []): Branch
    {
        $newLogoPath = Arr::get($params, 'logo');
        $oldLogoPath = Arr::get($params, 'persisted_logo');

        try {
            $branch = parent::update($id, Arr::except($params, ['id', 'persisted_logo']));

            if ($newLogoPath && $oldLogoPath && $oldLogoPath !== $newLogoPath) {
                Storage::disk('public')->delete($oldLogoPath);
            }

            return $branch;
        } catch (\Throwable $th) {
            if ($newLogoPath && $newLogoPath !== $oldLogoPath) {
                Storage::disk('public')->delete($newLogoPath);
            }

            Log::error(__METHOD__, ['message' => $th->getMessage(), 'id' => $id, 'params' => $params]);

            throw $th;
        }
    }

    public function delete($id)
    {
        try {
            $branch = $this->find($id);

            if (! $branch) {
                return [
                    'status' => false,
                    'message' => __('admin/branch.messages.not_found'),
                ];
            }

            if ($branch->products()->exists()) {
                return [
                    'status' => false,
                    'message' => __('admin/branch.messages.has_products'),
                ];
            }

            DB::transaction(function () use ($branch, $id) {
                $branch->update(['is_active' => GlobalConst::IS_NOT_ACTIVE]);
                parent::delete($id);
            });

            return [
                'status' => true,
                'message' => __('admin/branch.messages.deleted'),
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

            $branch = $this->find($id);
            $branch?->update(['is_active' => GlobalConst::IS_ACTIVE]);

            return $restored;
        } catch (\Throwable $th) {
            Log::error(__METHOD__, ['message' => $th->getMessage(), 'id' => $id]);

            throw $th;
        }
    }

    public function forceDelete($id)
    {
        try {
            $branch = $this->getRepository()->findWithTrashed($id);

            if (! $branch) {
                return false;
            }

            $logo = $branch->logo;

            $result = DB::transaction(fn () => parent::forceDelete($id));

            if ($result && $logo) {
                Storage::disk('public')->delete($logo);
            }

            return $result;
        } catch (\Throwable $th) {
            Log::error(__METHOD__, ['message' => $th->getMessage(), 'id' => $id]);

            throw $th;
        }
    }
}
