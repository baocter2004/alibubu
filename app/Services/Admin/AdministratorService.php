<?php

namespace App\Services\Admin;

use App\Const\AdminActivityConst;
use App\Const\AdminConst;
use App\Models\Admin;
use App\Repositories\AdminRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdministratorService
{
    public function __construct(protected AdminRepository $adminRepository) {}

    public function paginate(): LengthAwarePaginator
    {
        return $this->adminRepository->newQuery()
            ->latest('created_at')
            ->paginate(AdminConst::PER_PAGE)
            ->withQueryString();
    }

    public function assignableRoles(Admin $actor): array
    {
        $roles = AdminConst::roles();

        if ($actor->isSuperAdmin()) {
            return $roles;
        }

        return array_intersect_key($roles, array_flip(AdminConst::delegableRoleIds()));
    }

    public function canManage(Admin $actor, Admin $target): bool
    {
        return $actor->isSuperAdmin()
            || in_array((int) $target->role, AdminConst::delegableRoleIds(), true);
    }

    public function findManageable(Admin $actor, string $id): Admin
    {
        $target = $this->adminRepository->find($id);

        abort_if(! $target, 404);
        abort_unless($this->canManage($actor, $target), 403, __('admin/auth.messages.forbidden'));

        return $target;
    }

    public function create(Admin $actor, array $data): array
    {
        if (! array_key_exists((int) $data['role'], $this->assignableRoles($actor))) {
            return $this->fail('admin/administrator.messages.staff_only');
        }

        $admin = $this->adminRepository->newQuery()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => (int) $data['role'],
            'is_active' => (bool) $data['is_active'],
        ]);

        AdminActivityLogService::log(AdminActivityConst::ADMINISTRATORS_CREATED, $admin, [
            'email' => $admin->email,
            'role' => $admin->role,
            'is_active' => $admin->is_active,
        ]);

        return $this->success('admin/administrator.messages.created');
    }

    public function update(Admin $actor, string $id, array $data): array
    {
        return DB::transaction(function () use ($actor, $id, $data) {
            $activeSuperAdminIds = $this->lockActiveSuperAdminIds();
            $target = $this->adminRepository->newQuery()->lockForUpdate()->find($id);

            abort_if(! $target, 404);
            abort_unless($this->canManage($actor, $target), 403, __('admin/auth.messages.forbidden'));

            $role = (int) $data['role'];
            $isActive = (bool) $data['is_active'];
            $isSelf = (string) $target->id === (string) $actor->id;

            if (! array_key_exists($role, $this->assignableRoles($actor))) {
                return $this->fail('admin/administrator.messages.staff_only');
            }

            if ($isSelf && $role !== (int) $target->role) {
                return $this->fail('admin/administrator.messages.cannot_demote_self');
            }

            if ($isSelf && ! $isActive) {
                return $this->fail('admin/administrator.messages.cannot_deactivate_self');
            }

            $losesSuperAdmin = $target->isSuperAdmin()
                && $target->isActive()
                && ($role !== AdminConst::ROLE_SUPER_ADMIN || ! $isActive);

            if ($losesSuperAdmin && count($activeSuperAdminIds) <= 1) {
                return $this->fail('admin/administrator.messages.cannot_remove_last_super_admin');
            }

            $target->fill([
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => $role,
                'is_active' => $isActive,
            ]);

            if (filled($data['password'] ?? null)) {
                $target->password = $data['password'];
            }

            $changed = array_keys($target->getDirty());
            $mustRevoke = (bool) array_intersect($changed, ['role', 'is_active', 'password', 'email']);

            if ($mustRevoke) {
                $target->setRememberToken(Str::random(60));
            }

            $target->save();

            if ($mustRevoke) {
                $this->revokeSessions($target, $isSelf);
            }

            if ($changed) {
                AdminActivityLogService::log(AdminActivityConst::ADMINISTRATORS_UPDATED, $target, [
                    'changed' => array_values($changed),
                    'role' => $target->role,
                    'is_active' => $target->is_active,
                    'sessions_revoked' => $mustRevoke,
                ]);
            }

            return $this->success('admin/administrator.messages.updated');
        }, 3);
    }

    public function delete(Admin $actor, string $id): array
    {
        return DB::transaction(function () use ($actor, $id) {
            $activeSuperAdminIds = $this->lockActiveSuperAdminIds();
            $target = $this->adminRepository->newQuery()->lockForUpdate()->find($id);

            abort_if(! $target, 404);
            abort_unless($this->canManage($actor, $target), 403, __('admin/auth.messages.forbidden'));

            if ((string) $target->id === (string) $actor->id) {
                return $this->fail('admin/administrator.messages.cannot_delete_self');
            }

            if ($target->isSuperAdmin() && $target->isActive() && count($activeSuperAdminIds) <= 1) {
                return $this->fail('admin/administrator.messages.cannot_delete_last_super_admin');
            }

            $target->setRememberToken(Str::random(60));
            $target->save();
            $target->delete();

            $this->revokeSessions($target);

            AdminActivityLogService::log(AdminActivityConst::ADMINISTRATORS_DELETED, $target, [
                'email' => $target->email,
                'role' => $target->role,
            ]);

            return $this->success('admin/administrator.messages.deleted');
        }, 3);
    }

    public function revokeSessions(Admin $admin, bool $keepCurrent = false): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        DB::connection(config('session.connection'))
            ->table(config('session.table', 'sessions'))
            ->where('user_id', $admin->getKey())
            ->when($keepCurrent && request()->hasSession(), fn ($query) => $query->where('id', '!=', request()->session()->getId()))
            ->delete();
    }

    protected function lockActiveSuperAdminIds(): array
    {
        return $this->adminRepository->newQuery()
            ->where('role', AdminConst::ROLE_SUPER_ADMIN)
            ->where('is_active', true)
            ->orderBy('id')
            ->lockForUpdate()
            ->pluck('id')
            ->all();
    }

    protected function success(string $key): array
    {
        return ['status' => true, 'message' => __($key)];
    }

    protected function fail(string $key): array
    {
        return ['status' => false, 'message' => __($key)];
    }
}
