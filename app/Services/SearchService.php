<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

// use Illuminate\Support\Facades\Schema;

class SearchService
{
    protected $model;
    protected $with = [];
    protected $counts = [];
    protected $sums = [];

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function with($relations)
    {
        $this->with = Arr::wrap($relations);

        return $this;
    }

    public function counts($relations)
    {
        $this->counts = Arr::wrap($relations);

        return $this;
    }

    public function sums($relations)
    {
        $this->sums = Arr::wrap($relations);

        return $this;
    }


    public function search($request)
{
    $query = $this->model->newQuery();

    if ($request->has('search')) {
        $searchValue = $request->input('search');

        $table = $this->model->getTable();
        $columns = $this->getStringColumns($table);

        $query->where(function ($query) use ($columns, $searchValue) {
            foreach ($columns as $column) {
                $query->orWhere($column, 'LIKE', '%' . $searchValue . '%');
            }

            // Search in related models' string columns
            $relations = $this->model->getRelations();
            // dd($this->model->getRelations());

           //lineprepacks
           if ($this->model->getTable()=='line_prepack_pivot'){
             $query->orWhereHas('order',fn($q)=>$q->where('customer_name','LIKE', '%' . $searchValue . '%')
                                                  ->orWhere('shp_name','LIKE', '%' . $searchValue . '%')
                               );
            $query->orWhereHas('user',fn($q)=>$q->where('name','LIKE', '%' . $searchValue . '%')

                               );


           }

            foreach ($relations as $relationName => $relation) {
                $relatedTable = $relation->getRelated()->getTable();
                $relatedColumns = $this->getStringColumns($relatedTable);

                $query->orWhereHas($relationName, function ($query) use ($relatedColumns, $searchValue) {
                    foreach ($relatedColumns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $searchValue . '%');
                    }
                });
            }
        });
    }

    if (!empty($this->with)) {
        $query->with($this->with);
    }

    if (!empty($this->counts)) {
        $query->withCount($this->counts);
    }

    if (!empty($this->sums)) {
        foreach ($this->sums as $relation => $column) {
            $query->withSum($relation, $column);
        }
    }

    return $query->orderByDesc('id')
                 ->paginate(15)
                 ->withQueryString()
                 ->appends($request->all());
}


    protected function getStringColumns($table)
    {
        $columns = [];

        $schema = $this->model->getConnection()->getDoctrineSchemaManager();
        $tableColumns = $schema->listTableColumns($table);

        foreach ($tableColumns as $column) {
            if ($column->getType()->getName() === 'string') {
                $columns[] = $column->getName();
            }
        }

        return $columns;
    }
}
