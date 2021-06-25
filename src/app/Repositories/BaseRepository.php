<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\AbstractPaginator as Paginator;
use Illuminate\Support\Arr;

class BaseRepository
{
    /**
     * Model class for repo.
     *
     * @var string
     */
    protected $model;

    /**
     * @return EloquentQueryBuilder|QueryBuilder
     */
    protected function newQuery()
    {
        return app($this->model)->newQuery();
    }

    /**
     * @param EloquentQueryBuilder|QueryBuilder $query
     * @param int $take
     * @param bool $paginate
     *
     * @return EloquentCollection|Paginator
     */
    protected function doQuery($query = null, $take = 15, $paginate = true)
    {
        if (is_null($query)) {
            $query = $this->newQuery();
        }

        if (true == $paginate) {
            return $query->paginate($take);
        }

        if ($take > 0 || false !== $take) {
            $query->take($take);
        }

        return $query->get();
    }

    /**
     * Returns all records.
     * If $take is false then brings all records
     * If $paginate is true returns Paginator instance.
     *
     * @param int $take
     * @param bool $paginate
     *
     * @return EloquentCollection|Paginator
     */
    public function getAll($take = 15, $paginate = true)
    {
        return $this->doQuery(null, $take, $paginate);
    }

    /**
     * @param string $column
     * @param string|null $key
     *
     * @return \Illuminate\Support\Collection
     */
    public function lists($column, $key = null)
    {
        return $this->newQuery()->lists($column, $key);
    }

    /**
     * Retrieves a record by his id
     * If fail is true $ fires ModelNotFoundException.
     *
     * @param int $id
     * @param bool $fail
     *
     * @return Model
     */
    public function findByID($id, $fail = true)
    {
        if ($fail) {
            return $this->newQuery()->findOrFail($id);
        }

        return $this->newQuery()->find($id);
    }

    public function find(array $filters, array $withs = null): Builder
    {
        $query = $this->model::query();

        foreach ($filters as $column => $value) {
            if ($column !== 'order_by') {
                $query->when($value, function ($query, $value) use ($column) {
                    return is_array($value)
                        ? $query->whereIn($column, $value)
                        : $query->where($column, $value);
                });
            }
        }

        Arr::set($filters, 'order_by', Arr::get($filters, 'order_by') ?? 'created_at');
        $query->when(Arr::get($filters, 'order_by'), function ($query, $column) {
            return $query->orderBy($column, $column === 'created_at' ? 'DESC' : 'ASC');
        });

        $query->when($withs, function ($query, $withs) {
            foreach ($withs as $table) {
                $query = $query->with($table);
            }
        });

        return $query;
    }
}
