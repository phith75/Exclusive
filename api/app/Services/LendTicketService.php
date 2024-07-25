<?php

namespace App\Services;

use App\Enum\LendTicketStatus;
use App\Repositories\LendTicketRepository;
use App\Utils\Constants;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class LendTicketService
{
    protected $ticketRepo;
    protected $bookService;

    public function __construct(LendTicketRepository $ticketRepo, BookService $bookService)
    {
        $this->ticketRepo = $ticketRepo;
        $this->bookService = $bookService;
    }

    public function getAll($perPage, array $relation): LengthAwarePaginator
    {
        return $this->ticketRepo->searchAndPaginate($perPage, $relation);
    }

    public function getPaginate(Request $data): LengthAwarePaginator
    {
        $perPage = Constants::DEFAULT_PER_PAGE;
        $relation = ['ticketDetail', 'user'];
        $dataSearch = [
            'keywords' => $data->get('keywords', ''),
            'status' => $data->get('status', ''),
            'borrowed_date' => $data->get('borrowed_date', ''),
            'ticketDetail.status' => $data->get('ticketDetail', ''),
        ];
        $dateFilter = [
            'start_date' => $data->get('start_date', ''),
            'end_date' => $data->get('end_date', ''),
        ];
        $searchColumns = [
            'name',
            'email',
            'phone',
            'lend_ticket_code',
            'status',
            'borrowed_date',
            'note',
        ];
        return $this->ticketRepo->searchAndPaginate($perPage, $relation, $dataSearch, $searchColumns, $dateFilter);
    }

    public function find($id): ?object
    {
        $relation = ['ticketDetail', 'user', 'books'];

        return $this->ticketRepo->find($id, $relation);
    }

    public function findByUserId(int $id, Request $data): ?object
    {
        $perPage = Constants::DEFAULT_PER_PAGE;
        $relation = ['ticketDetail', 'user'];
        $dataSearch = [
            'keywords' => $data->get('keywords', ''),
            'status' => $data->get('status', ''),
            'borrowed_date' => $data->get('borrowed_date', ''),
            'ticketDetail.status' => $data->get('ticketDetail', ''),
        ];
        $dateFilter = [
            'start_date' => $data->get('start_date', ''),
            'end_date' => $data->get('end_date', ''),
        ];
        $searchColumns = [
            'name',
            'email',
            'phone',
            'lend_ticket_code',
            'status',
            'borrowed_date',
            'note',
        ];
        return $this->ticketRepo->findByUserId($id, $perPage, $relation, $dataSearch, $searchColumns, $dateFilter);
    }

    public function create(array $data): null
    {
        $data['status'] = LendTicketStatus::APPROVED;
        $data['borrowed_date'] = date('Y-m-d');
        foreach ($data['book_data'] as $book) {
            $this->bookService->incrementBorrowedCount($book['book_id'], $book['quantity']);
        }

        return $this->ticketRepo->create($data)->books()->attach($data['book_data']);
    }

    public function update(int $id, array $data): bool
    {
        return $this->ticketRepo->update($id, $data);
    }

    public function delete(int|array $id): int
    {
        return $this->ticketRepo->delete($id);
    }

    public function restore(int|array $ids): int
    {
        return $this->ticketRepo->restore($ids);
    }
}
