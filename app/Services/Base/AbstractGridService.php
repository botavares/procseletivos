<?php
namespace App\Services\Base;

use CodeIgniter\Model;

class AbstractGridService
{
    protected Model $model;

    protected array $columns = [];
    protected array $orderBy = [];

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function setColumns(array $columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    public function setOrder(string $field, string $direction = 'asc'): self
    {
        $this->orderBy = [$field, $direction];
        return $this;
    }

    public function get(array $filters = []): array
    {
        $builder = $this->model;

        foreach ($filters as $field => $value) {
            $builder = $builder->where($field, $value);
        }

        if ($this->orderBy) {
            $builder = $builder->orderBy(
                $this->orderBy[0],
                $this->orderBy[1]
            );
        }

        return [
            'data'    => $builder->findAll(),
            'columns' => $this->columns
        ];
    }
}



