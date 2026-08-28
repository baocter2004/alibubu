<?php

namespace App\Traits;

trait HasBuildQuery
{
    protected function buildWhereBetween(array $params)
    {
        $data = [];
        foreach ($params as $key => $value) {
            if (empty($value)) continue;
            $data[$key] = is_string($value) ? explode(',', $value) : $value;
        }
        return $data;
    }

    protected function buildWhereEqual(array $params)
    {
        return $this->cleanValueNull($params);
    }

    protected function buildWhereIn(array $params)
    {
        return $this->cleanValueNull($params);
    }

    protected function buildWhereLike(array $params)
    {
        $wheres = [];
        $params = $this->cleanValueNull($params);
        foreach ($params as $key => $value) {
            if (empty($value)) continue;
            $wheres[] = [$key, 'LIKE', '%' . $value . '%'];
        }
        return $wheres;
    }

    protected function buildSort($sort)
    {
        if (empty($sort) || !str_contains($sort, ':')) return [];
        $sorts = explode(':', $sort);

        if (count($sorts) !== 2 || !in_array(strtolower($sorts[1]), ['asc', 'desc'])) {
            return [];
        }

        $column = str_replace('raw|', '', $sorts[0]);

        if (!preg_match('/^[A-Za-z0-9_.]+$/', $column)) {
            return [];
        }

        return [
            'column'    => $sorts[0],
            'direction' => strtolower($sorts[1]),
        ];
    }

    protected function cleanValueNull(array $params)
    {
        return array_filter($params, function ($value) {
            return $value !== null;
        });
    }
}
