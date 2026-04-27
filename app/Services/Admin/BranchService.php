<?php

namespace App\Services\Admin;

use App\Models\Branch;
use App\Repositories\BranchRepository;
use App\Services\BaseCrudService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BranchService extends BaseCrudService
{
    protected function getRepository(): BranchRepository
    {
        if (empty($this->repository)) {
            $this->repository = app()->make(BranchRepository::class);
        }

        return $this->repository;
    }

    protected function buildFilterParams(array $params): array
    {
        $wheres = Arr::get($params, 'wheres', []);
        $whereIns = Arr::get($params, 'where_ins', []);
        $whereLikes = Arr::get($params, 'where_likes', []);
        $whereEquals = Arr::get($params, 'where_equals', []);
        $orWheres = Arr::get($params, 'or_wheres', []);
        $sort = Arr::get($params, 'sort', 'created_at:desc');
        $relates = Arr::get($params, 'relates', []);
        $relatesCount = Arr::get($params, 'relates_count', []);

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
        $data = $validated;
        $data['id'] = $id;

        if (!empty($validated['logo']) && $validated['logo'] instanceof \Illuminate\Http\UploadedFile) {
            if (!empty($oldSessionData['logo'])) {
                Storage::disk('public')->delete($oldSessionData['logo']);
            }

            $logoPath = $validated['logo']->store('branches', 'public');
            $data['logo'] = $logoPath;
        } elseif (!empty($id)) {
            $branch = $this->find($id);
            if ($branch) {
                $data['logo'] = $branch->logo;
            }
        }

        return $data;
    }

    public function create(array $params = []): Branch
    {
        $logoPath = Arr::get($params, 'logo');

        try {
            return parent::create($params);
        } catch (\Exception $e) {
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }
            Log::error('Error creating branch: ' . $e->getMessage(), ['params' => $params]);
            throw $e;
        }
    }

    public function update(int|string $id, array $params = []): Branch
    {
        $newLogoPath = Arr::get($params, 'logo');

        try {
            $branch = $this->find($id);
            $oldLogoPath = $branch?->logo;

            $result = parent::update($id, $params);

            if ($newLogoPath && $oldLogoPath && $oldLogoPath !== $newLogoPath) {
                Storage::disk('public')->delete($oldLogoPath);
            }

            return $result;
        } catch (\Exception $e) {
            if ($newLogoPath) {
                Storage::disk('public')->delete($newLogoPath);
            }
            Log::error('Error updating branch: ' . $e->getMessage(), ['id' => $id, 'params' => $params]);
            throw $e;
        }
    }
}
