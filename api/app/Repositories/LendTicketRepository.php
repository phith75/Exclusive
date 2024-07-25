<?php

namespace App\Repositories;

use App\Utils\Constants;
use App\Models\LendTicket;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LendTicketRepository extends BaseRepository
{
    protected $model;

    public function getModel(): string
    {
        return LendTicket::class;
    }

    public function updateTicketStatus(int $id, int $status): bool
    {
        return $this->model->where('id', $id)->update(['status' => $status]);
    }

    public function createLendticket(array $data): mixed
    {
        return $this->model->create($data);
    }

    public function findByUserId(int $id, int $perPage = Constants::DEFAULT_PER_PAGE, array $relation = [], array $dataSearch = [], array $searchColumns = ['name'], array $dateFilter = []): LengthAwarePaginator
    {
        $query = $this->model->with($relation);
        if (!empty($dateFilter['start_date'])) {
            $query->where('borrowed_date', '>=', $dateFilter['start_date']);
        }
        if (!empty($dateFilter['end_date'])) {
            $query->where('borrowed_date', '<=', $dateFilter['end_date']);
        }
        $query = $this->buildSearchQuery($query, $dataSearch, $searchColumns);
        return $query->where('user_id', $id)->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page');
    }
    public function searchAndPaginate(int $perPage = Constants::DEFAULT_PER_PAGE, array $relation = [], array $dataSearch = [], array $searchColumns = ['name'], array $dateFilter = [])
    {
        $query = $this->model->with($relation);
        if (!empty($dateFilter['start_date'])) {
            $query->where('borrowed_date', '>=', $dateFilter['start_date']);
        }
        if (!empty($dateFilter['end_date'])) {
            $query->where('borrowed_date', '<=', $dateFilter['end_date']);
        }
        $query = $this->buildSearchQuery($query, $dataSearch, $searchColumns);
        return $query->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page');
    }
}
