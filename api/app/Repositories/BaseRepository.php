<?php

namespace App\Repositories;

use App\Models\LendTicketDetail;
use App\Utils\Constants;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements RepositoryInterface
{
    protected $model;

    public function __construct()
    {
        $this->setModel();
    }

    abstract public function getModel();

    public function setModel(): void
    {
        $this->model = app()->make($this->getModel());
    }

    protected function buildSearchQuery($query, array $dataSearch, array $searchColumns)
    {
        foreach ($dataSearch as $key => $value) {
            $query->when(
                !empty($value),
                function ($q) use ($key, $value, $dataSearch, $searchColumns) {
                    if (isset($dataSearch['keywords']) && $key === 'keywords') {
                        $q->where(function ($query) use ($dataSearch, $searchColumns) {
                            foreach ($searchColumns as $column) {
                                $columnParts = explode('.', $column);
                                if (count($columnParts) > 1) {
                                    $relation = $columnParts[0];
                                    $columnName = $columnParts[1];
                                    $keywords = $dataSearch['keywords'];
                                    if ($columnName === "slug") {
                                        $keywords = str_replace(' ', '-', $keywords);
                                        $keywords = preg_replace('/-+/', '-', $keywords);
                                        $keywords = trim($keywords, '-');
                                    }
                                    $query->orWhereHas($relation, function ($q) use ($columnName, $keywords) {
                                        $q->where($columnName, 'like', '%' . mb_strtoupper($keywords, 'UTF-8') . '%');
                                    });
                                } else {
                                    // Direct column search
                                    $query->orWhere($column, 'like', '%' . mb_strtoupper($dataSearch['keywords'], 'UTF-8') . '%');
                                }
                            }
                        });
                    } else {
                        // Handling for other columns
                        $columnParts = explode('.', $key);
                        if (count($columnParts) > 1) {
                            $relation = $columnParts[0];
                            $columnName = $columnParts[1];
                            $q->whereHas($relation, function ($query) use ($columnName, $value) {
                                $query->where($columnName, $value);
                            });
                        } else {
                            $q->where($key, $value);
                        }
                    }
                }
            );
        }

        return $query;
    }

    public function searchAndPaginate(int $perPage = Constants::DEFAULT_PER_PAGE, array $relation = [], array $dataSearch = [], array $searchColumns = ['name'])
    {
        $query = $this->model->with($relation);
        $query = $this->buildSearchQuery($query, $dataSearch, $searchColumns);
        return $query->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page');
    }

    public function getAll(): Collection
    {
        return $this->model->query()->orderBy('created_at', 'desc')->get();
    }

    public function find(int $id, array $relation = []): ?object
    {
        return $this->model->with($relation)->findOrFail($id);
    }

    public function findOrFail(int $id, array $relation = []): ?object
    {
        return $this->model->with($relation)->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $record = $this->model->findOrFail($id);

        return $record->update($data);
    }

    public function delete(int|array $id): int
    {
        $this->model->findOrFail($id);

        return $this->model->destroy($id);
    }

    public function getPaginate(int $perPage): LengthAwarePaginator
    {
        return $this->model->paginate($perPage, ['*'], 'page');
    }

    public function getWithRelation(int $perPage, array $relation = []): LengthAwarePaginator
    {
        return $this->model->with($relation)->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getTrashed(int $perPage, array $relation = [], array $dataSearch = [], array $searchColumns = ['name']): LengthAwarePaginator
    {
        $query = $this->model->with($relation);
        $query = $this->buildSearchQuery($query, $dataSearch, $searchColumns);

        return $query->onlyTrashed()->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page')->withQueryString();
    }

    public function restore(array $ids): int
    {
        $authors = $this->model->onlyTrashed()->whereIn('id', $ids)->get();
        $restoredCount = 0;

        foreach ($authors as $author) {
            if ($author->restore()) {
                $restoredCount++;
            }
        }

        return $restoredCount;
    }
}
