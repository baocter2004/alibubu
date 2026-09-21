<?php

namespace App\Services\Admin;

use App\Const\AdminActivityConst;
use App\Const\AdminConst;
use App\Const\PermissionConst;
use App\Repositories\AdminRepository;
use App\Repositories\AdminRolePermissionRepository;
use Illuminate\Support\Facades\Cache;

class RoleService
{
    protected ?array $matrix = null;

    public function __construct(
        protected AdminRolePermissionRepository $rolePermissionRepository,
        protected AdminRepository $adminRepository,
    ) {}

    public function matrix(): array
    {
        return $this->matrix ??= $this->load();
    }

    public function permissionsFor(int $role): array
    {
        if ($role === AdminConst::ROLE_SUPER_ADMIN) {
            return PermissionConst::all();
        }

        return $this->matrix()[$role] ?? [];
    }

    public function roleHasPermission(int $role, string $permission): bool
    {
        return in_array($permission, $this->permissionsFor($role), true);
    }

    public function isUsingDefaults(): bool
    {
        return empty($this->stored());
    }

    public function grantablePermissions(): array
    {
        return array_values(array_diff(PermissionConst::all(), PermissionConst::superAdminOnly()));
    }

    public function update(array $permissionsByRole): array
    {
        $before = $this->matrix();
        $matrix = [];

        foreach (AdminConst::editableRoleIds() as $role) {
            $matrix[$role] = array_values(array_intersect(
                $this->grantablePermissions(),
                (array) ($permissionsByRole[$role] ?? [])
            ));
        }

        $this->persist($matrix);

        AdminActivityLogService::log(AdminActivityConst::ROLES_PERMISSIONS_UPDATED, null, [
            'changes' => $this->diff($before, $matrix),
        ]);

        return $matrix;
    }

    public function resetToDefaults(): array
    {
        $before = $this->matrix();
        $matrix = PermissionConst::defaults();

        $this->persist($matrix);

        AdminActivityLogService::log(AdminActivityConst::ROLES_PERMISSIONS_RESET, null, [
            'changes' => $this->diff($before, $matrix),
        ]);

        return $matrix;
    }

    public function seedDefaults(): void
    {
        if (! $this->isUsingDefaults()) {
            return;
        }

        $this->persist(PermissionConst::defaults());
    }

    public function adminCountsByRole(): array
    {
        $counts = $this->adminRepository->newQuery()
            ->selectRaw('role, count(*) as aggregate')
            ->groupBy('role')
            ->pluck('aggregate', 'role')
            ->all();

        return collect(AdminConst::allRoleIds())
            ->mapWithKeys(fn (int $role) => [$role => (int) ($counts[$role] ?? 0)])
            ->all();
    }

    public function flush(): void
    {
        $this->matrix = null;
        Cache::forget(PermissionConst::CACHE_KEY);
    }

    protected function persist(array $matrix): void
    {
        $matrix[AdminConst::ROLE_SUPER_ADMIN] = [PermissionConst::SUPER_ADMIN_MARKER];

        $this->rolePermissionRepository->replaceAll($matrix);
        $this->flush();
    }

    protected function stored(): array
    {
        return Cache::rememberForever(PermissionConst::CACHE_KEY, fn () => $this->rolePermissionRepository->groupedByRole());
    }

    protected function load(): array
    {
        $stored = $this->stored();

        if (empty($stored)) {
            return PermissionConst::defaults();
        }

        $matrix = [];

        foreach (AdminConst::editableRoleIds() as $role) {
            $matrix[$role] = array_values(array_intersect($this->grantablePermissions(), $stored[$role] ?? []));
        }

        return $matrix;
    }

    protected function diff(array $before, array $after): array
    {
        $changes = [];

        foreach (AdminConst::editableRoleIds() as $role) {
            $granted = array_values(array_diff($after[$role] ?? [], $before[$role] ?? []));
            $revoked = array_values(array_diff($before[$role] ?? [], $after[$role] ?? []));

            if ($granted || $revoked) {
                $changes[$role] = ['granted' => $granted, 'revoked' => $revoked];
            }
        }

        return $changes;
    }
}
