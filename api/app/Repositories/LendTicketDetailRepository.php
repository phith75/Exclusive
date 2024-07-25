<?php

namespace App\Repositories;

use App\Models\LendTicketDetail;
use Illuminate\Database\Eloquent\Collection;

class LendTicketDetailRepository extends BaseRepository
{
    protected $model;

    public function getModel()
    {
        return LendTicketDetail::class;
    }

    public function findByTicketId(int $id): Collection
    {
        return $this->model->where('lend_ticket_id', $id)->with('book:id,name')->get();
    }

    public function updateStatus(int $id, int $status): bool
    {
        return $this->model->where('id', $id)->update(['status' => $status]);
    }
}
