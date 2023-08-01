<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SearchQueryService
{
    protected $queryBuilder;
    protected $searchParameter;
    protected $searchColumns;
    protected $strictColumns;
    protected $relatedModels;
    protected $withModels = [];
    protected $withSumColumns = [];
    protected $withCountModels = [];

    public function __construct(Builder $queryBuilder, $searchParameter, array $searchColumns, array $strictColumns = [], array $relatedModels = [])
    {
        $this->queryBuilder = $queryBuilder;
        $this->searchParameter = $searchParameter;
        $this->searchColumns = $searchColumns;
        $this->strictColumns = $strictColumns;
        $this->relatedModels = $relatedModels;
    }

    public function with(array $withModels)
    {
        $this->withModels = $withModels;

        return $this;
    }

    public function withSum(array $withSumColumns)
    {
        $this->withSumColumns = $withSumColumns;

        return $this;
    }

    public function withCount(array $withCountModels)
    {
        $this->withCountModels = $withCountModels;

        return $this;
    }

    public function search()
    {
        if (!empty($this->searchParameter) && count($this->searchColumns) > 0) {
            $this->queryBuilder->where(function ($query) {
                $this->applySearchFilters($query, $this->searchColumns, 'like');
                $this->applySearchFilters($query, $this->strictColumns, '=');

                if (count($this->relatedModels) > 0) {
                    foreach ($this->relatedModels as $relatedModel => $relatedColumns) {
                        $query->orWhereHas($relatedModel, function ($query) use ($relatedColumns) {
                            $this->applySearchFilters($query, $relatedColumns, 'like');
                        });
                    }
                }
            });
        }

        if (count($this->withModels) > 0) {
            $this->queryBuilder->with($this->withModels);
        }

        if (count($this->withSumColumns) > 0) {
            foreach ($this->withSumColumns as $relatedModel => $column) {
                $this->queryBuilder->withSum($relatedModel, $column);
            }
        }

        if (count($this->withCountModels) > 0) {
            foreach ($this->withCountModels as $relatedModel => $column) {
                $this->queryBuilder->withCount($relatedModel . ' as ' . $column);
            }
        }

        return $this->queryBuilder;


    }

    protected function applySearchFilters($query, $columns, $operator)
    {
        $searchParameter = $this->searchParameter;

        $query->where(function ($query) use ($columns, $operator, $searchParameter) {
            foreach ($columns as $column) {
                if ($operator === '=') {
                    $query->orWhere($column, '=', $searchParameter);
                } elseif ($operator === 'like') {
                    $query->orWhere($column, 'like', "%{$searchParameter}%");
                }
            }
        });
    }
}
