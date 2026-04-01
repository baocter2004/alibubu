<?php

namespace App\Repositories;

use App\Traits\HasBuildQuery;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class BaseRepository
{
    use HasBuildQuery;

    protected Model $model;

    abstract protected function getModel(): Model;

    public function __construct()
    {
        $this->setModel();
    }

    private function setModel()
    {
        $model = $this->getModel();
        if (!($model instanceof Model)) {
            throw new \Exception('Model not found');
        }
        $this->model = $model;
    }

    public function newQuery(): Builder
    {
        return $this->getModel()->newQuery();
    }

    public function find(int|string $id): ?Model
    {
        return $this->findBy($id);
    }

    public function findBy($value, $column = 'id'): ?Model
    {
        return $this->newQuery()->where($column, $value)->first();
    }

    public function filter(array $params): Builder
    {
        $query = $this->newQuery();

        // query common field
        $whereBetweens = $this->buildWhereBetween($params['where_betweens'] ?? []);
        $whereEquals = $this->buildWhereEqual(array_merge($params['where_equals'] ?? [], $params['wheres'] ?? []));
        $whereLikes = $this->buildWhereLike(array_merge($params['where_likes'] ?? [], $params['likes'] ?? []));
        $whereIns = $this->buildWhereIn($params['where_ins'] ?? []);
        $whereNotIns = $this->buildWhereIn($params['where_not_ins'] ?? []);
        $whereHas = $this->cleanValueNull($params['where_has'] ?? []);
        $whereDoesntHave = $this->cleanValueNull($params['where_doesnt_have'] ?? []);
        $orWheres = $this->cleanValueNull($params['or_wheres'] ?? []);
        $inMonth = $this->cleanValueNull($params['in_month'] ?? []);
        $whereRaws = $this->cleanValueNull($params['where_raw'] ?? []);
        $notNull = $this->cleanValueNull($params['not_null'] ?? []);
        $isNull = $this->cleanValueNull($params['is_null'] ?? []);
        $inFieldArray = $this->cleanValueNull($params['in_field_array'] ?? []);
        $sort = $this->buildSort($params['sort'] ?? '');
        $relates = $params['relates'] ?? null;
        $relatesCount = $params['relates_count'] ?? null;
        $withoutDomainFilter = $params['withoutDomainFilter'] ?? false;

        // multi sort
        $sorts = [];
        $sortsParam = ($params['sorts'] ?? []);
        if (is_array($sortsParam)) {
            foreach ($sortsParam as $sortParam) {
                $sorts[] = $this->buildSort($sortParam);
            }
        }

        $query
            ->when($notNull, function ($query) use ($notNull) {
                $query->where(function ($query) use ($notNull) {
                    foreach ($notNull as $name) {
                        if ($name) {
                            $query->whereNotNull($name);
                            $query->where($name, '!=', '');
                        }
                    }
                });
            })
            ->when($isNull, function ($query) use ($isNull) {
                foreach ($isNull as $name) {
                    if ($name) {
                        $query->where(function ($query) use ($isNull, $name) {
                            $query->whereNull($name);
                            $query->orWhere($name, '');
                        });
                    }
                }
            })
            ->when($whereRaws, function ($query) use ($whereRaws) {
                $query->whereRaw($whereRaws);
            })
            ->when($inMonth, function ($query) use ($inMonth) {
                foreach ($inMonth as $key => $monthYear) {
                    $query->whereYear($key, Carbon::parse($monthYear)->format('Y'));
                    $query->whereMonth($key, Carbon::parse($monthYear)->format('m'));
                }
            })
            ->when($whereEquals, function ($query) use ($whereEquals) {
                $query->where($whereEquals);
            })
            ->when($whereIns, function ($query) use ($whereIns) {
                foreach ($whereIns as $key => $in)
                    $query->whereIn($key, $in);
            })
            ->when($whereNotIns, function ($query) use ($whereNotIns) {
                foreach ($whereNotIns as $key => $in)
                    $query->whereNotIn($key, $in);
            })
            ->when($whereLikes, function ($query) use ($whereLikes) {
                $query->where($whereLikes);
            })
            ->when(!empty($whereHas), function ($query) use ($whereHas) {
                foreach ($whereHas as $relateName => $conditions) {
                    if (!empty($conditions)) {
                        if (is_array($conditions)) {
                            $query->whereHas($relateName, function ($subQuery) use ($conditions) {
                                foreach ($conditions as $column => $condition) {
                                    if (is_array($condition) && ($condition[0] ?? false) && ($condition[2] ?? false) && strtoupper($condition[1] ?? false) === 'IN') {
                                        $subQuery->whereIn($condition[0], $condition[2]);
                                    } else if (is_callable($condition)) {
                                        $subQuery->where($condition);
                                    } else if (is_array($condition) && $condition[0] === 'LIKE') {
                                        $subQuery->where($column, 'LIKE', "%$condition[1]%");
                                    } else if (is_array($condition)) {
                                        $subQuery->where([$condition]);
                                    } else {
                                        $subQuery->where($column, $condition);
                                    }
                                }
                            });
                        } else {
                            $query->whereHas($relateName, $conditions);
                        }
                    }
                }
            })
            ->when(!empty($whereDoesntHave), function ($query) use ($whereDoesntHave) {
                foreach ($whereDoesntHave as $relateName => $conditions) {
                    if (!empty($conditions)) {
                        if (is_array($conditions)) {
                            $query->whereDoesntHave($relateName, function ($subQuery) use ($conditions) {
                                foreach ($conditions as $column => $condition) {
                                    if (is_array($condition) && ($condition[0] ?? false) && ($condition[2] ?? false) && strtoupper($condition[1] ?? false) === 'IN') {
                                        $subQuery->whereIn($condition[0], $condition[2]);
                                    } else if (is_callable($condition)) {
                                        $subQuery->where($condition);
                                    } else if (is_array($condition) && $condition[0] === 'LIKE') {
                                        $subQuery->where($column, 'LIKE', "%$condition[1]%");
                                    } else if (is_array($condition)) {
                                        $subQuery->where([$condition]);
                                    } else {
                                        $subQuery->where($column, $condition);
                                    }
                                }
                            });
                        } else {
                            $query->whereDoesntHave($relateName, $conditions);
                        }
                    }
                }
            })
            ->when($inFieldArray, function ($query) use ($inFieldArray) {
                foreach ($inFieldArray as $key => $value) {
                    $query->whereRaw("FIND_IN_SET($value, REPLACE(REPLACE ( REPLACE ( $key, '[', '' ), ']', '' ),' ', '')) > 0");
                }
            })
            ->when(!empty($orWheres), function ($query) use ($orWheres) {
                $query->where(function ($query) use ($orWheres) {
                    foreach ($orWheres as $value) {
                        if (is_array($value) && count($value) === 3) {
                            $query->orWhere($value[0], $value[1], $value[2]);
                        } else {
                            $query->orWhere($value);
                        }
                    }
                });
            })
            ->when(!empty($sorts), function ($query) use ($sorts) {
                foreach ($sorts as $sort) {
                    if (!empty($sort)) {
                        if (str_contains($sort['column'], 'raw|')) {
                            $sort['column'] = str_replace('raw|', '', $sort['column']);
                            $query->orderByRaw($sort['column'] . ' ' . $sort['direction']);
                        } else {
                            $query->orderBy($sort['column'], $sort['direction']);
                        }
                    }
                }
            })
            ->when(!empty($sort), function ($query) use ($sort) {
                if (str_contains($sort['column'], 'raw|')) {
                    $sort['column'] = str_replace('raw|', '', $sort['column']);
                    $query->orderByRaw($sort['column'] . ' ' . $sort['direction']);
                } else {
                    $query->orderBy($sort['column'], $sort['direction']);
                }
            })
            ->when(!empty($relates), function ($query) use ($relates) {
                $query->with($relates);
            })
            ->when(!empty($relatesCount), function ($query) use ($relatesCount) {
                $query->withCount($relatesCount);
            })
            ->when($withoutDomainFilter, function ($query) {
                $query->withoutDomainFilter();
            })
            ->when(!empty($whereBetweens), function ($query) use ($whereBetweens) {
                foreach ($whereBetweens as $column => $range) {
                    $query->whereBetween($column, $range);
                }
            });

        return $query;
    }

    public function get(array $params = []): Collection
    {
        return $this->filter($params)->get();
    }

    public function paginate(array $params = [], $limit = 10): LengthAwarePaginator
    {
        return $this->filter($params)->paginate($limit);
    }

    public function create(array $params): Model
    {
        return $this->newQuery()->create($params);
    }

    public function update($id, array $params): ?Model
    {
        $result = $this->newQuery()->find($id);
        $result->fill($params);
        $saved = $result->save();

        return $saved ? $result : null;
    }

    public function createOrUpdate(array $params, $instance = null): Model
    {
        $model = $this->getModel();
        if (!empty($instance) && $instance instanceof $model) {
            $model = $instance;
        }

        $model->fill($params);
        $model->save();

        return $model;
    }

    public function delete($id)
    {
        $model = $this->newQuery()->find($id);
        if ($model) {
            return $model->delete();
        }
        return false;
    }

    public function forceDelete(int $id)
    {
        $model = $this->newQuery()->find($id);
        return $model->forceDelete();
    }

    public function deleteAll(array $ids)
    {
        return $this->newQuery()->whereIn('id', $ids)->delete();
    }

    public function insert(array $params)
    {
        return $this->newQuery()->insert($params);
    }

    public function deleteBy($value, $column = 'id')
    {
        return $this->newQuery()->where($column, $value)->delete();
    }

    public function getTable()
    {
        return $this->newQuery()->getTable();
    }

    public function lastest()
    {
        return $this->newQuery()->withTrashed()->orderBy('id', 'DESC')->first();
    }

    public function existOrNot($value, $column)
    {
        return $this->newQuery()->where($column, $value)->exists();
    }

    public function restore(int $id)
    {
        return $this->newQuery()->onlyTrashed()->where('id', $id)->restore();
    }

    public function findTrashed(int $id)
    {
        return $this->newQuery()->onlyTrashed()->where('id', $id)->first();
    }

    public function findWithTrashed(int $id)
    {
        return $this->newQuery()->withTrashed()->where('id', $id)->first();
    }

    public function updateOrCreate(array $values, array $attributes = []): ?Model
    {
        return $this->newQuery()->updateOrCreate($attributes, $values);
    }

    public function upsert(array $params, array $uniqueByColumns, array $updatedColumns = null)
    {
        return $this->model->upsert($params, $uniqueByColumns, $updatedColumns);
    }

    public function countAll(): int
    {
        return $this->newQuery()->count();
    }
}
