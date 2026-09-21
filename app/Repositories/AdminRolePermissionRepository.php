<?php

namespace App\Repositories;

use App\Models\AdminRolePermission;
use Illuminate\Support\Facades\DB;

class AdminRolePermissionRepository extends BaseRepository
{
    public function getModel(): AdminRolePermission
    {
        if (empty($this->model)) {
            $this->model = app()->make(AdminRolePermission::class);
        }

        return $this->model;
    }

    public function groupedByRole(): array
    {
        return $this->newQuery()
            ->get(['role', 'permission'])
            ->groupBy('role')
            ->map(fn ($rows) => $rows->pluck('permission')->values()->all())
            ->all();
    }

    public function replaceAll(array $matrix): void
    {
        $now = now();
        $rows = [];

        foreach ($matrix as $role => $permissions) {
            foreach (array_unique($permissions) as $permission) {
                $rows[] = [
                    'role' => (int) $role,
                    'permission' => $permission,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::transaction(function () use ($rows) {
            $this->newQuery()->delete();

            foreach (array_chunk($rows, 200) as $chunk) {
                $this->newQuery()->insert($chunk);
            }
        });
    }
}
