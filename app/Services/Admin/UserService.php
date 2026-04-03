<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\BaseCrudService;
use Illuminate\Support\Arr;

class UserService extends BaseCrudService
{
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
        $sort = Arr::get($params, 'sort', 'created_at:desc');
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

    public function find(int|string $id): ?User
    {
        return $this->repository->filter([
            'wheres' => ['id' => $id],
        ])->first();
    }
}
