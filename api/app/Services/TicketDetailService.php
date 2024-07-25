<?php

namespace App\Services;

use App\Enum\TicketDetailStatus;
use App\Exceptions\InvalidStatusTransitionException;
use App\Models\LendTicketDetail;
use App\Repositories\LendTicketDetailRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TicketDetailService
{
    protected $ticketRepo;

    public function __construct(LendTicketDetailRepository $ticketRepo)
    {
        $this->ticketRepo = $ticketRepo;
    }

    public function getAll(): Collection
    {
        return $this->ticketRepo->getAll();
    }

    public function getPaginate(int $perPage): LengthAwarePaginator
    {
        return $this->ticketRepo->getPaginate($perPage);
    }

    public function find(int $id): ?object
    {
        return $this->ticketRepo->find($id);
    }

    public function create(array $data): LendTicketDetail
    {
        return $this->ticketRepo->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->ticketRepo->update($id, $data);
    }

    public function delete(int|array $id): int
    {
        return $this->ticketRepo->delete($id);
    }

    public function updateStatus(int $id, int $status): bool
    {
        $ticketDetail = $this->ticketRepo->findOrFail($id);
        $currentStatus = TicketDetailStatus::from($ticketDetail->status);
        $newStatus = TicketDetailStatus::from($status);

        if ($currentStatus == TicketDetailStatus::RETURNED) {
            throw new InvalidStatusTransitionException("Cannot update status from {$currentStatus->label()} to {$newStatus->label()}");
        } elseif ($currentStatus == TicketDetailStatus::OVERDUE && $newStatus == TicketDetailStatus::BORROWED) {
            throw new InvalidStatusTransitionException("Cannot update status from {$currentStatus->label()} to {$newStatus->label()}");
        }
        if (in_array($currentStatus, [TicketDetailStatus::BORROWED, TicketDetailStatus::OVERDUE, TicketDetailStatus::LOST])) {
            if ($newStatus->value == TicketDetailStatus::RETURNED->value) {
                $now = date('Y-m-d H:i:s');
                $this->update($id, ['returned_date' => $now]);
            }

            return $this->ticketRepo->updateStatus($id, $newStatus->value);
        }

        return false;
    }
}
