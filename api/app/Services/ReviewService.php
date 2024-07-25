<?php

namespace App\Services;

use App\Models\Review;
use App\Repositories\ReviewRepository;
use App\Utils\Constants;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;

class ReviewService
{
    protected $reviewRepo;

    public function __construct(ReviewRepository $reviewRepo)
    {
        $this->reviewRepo = $reviewRepo;
    }

    public function getAll(): Collection
    {
        return $this->reviewRepo->getAll();
    }

    public function getPaginate(Request $request): LengthAwarePaginator
    {
        $perPage = Constants::DEFAULT_PER_PAGE;
        $dataSearch['keywords'] = $request->get('keywords', '');
        $dataSearch['status'] = $request->get('status', '');
        $dataSearch['rating'] = $request->get('rating', '');
        $relation = ['user', 'book'];
        $searchColumns = ['description', 'book.name', 'user.name'];

        return $this->reviewRepo->searchAndPaginate($perPage, $relation, $dataSearch, $searchColumns);
    }

    public function find(int $id): ?object
    {
        return $this->reviewRepo->getPaginateOnlyBook($id);
    }

    public function update(int $id, array $data): bool
    {
        return $this->reviewRepo->update($id, $data);
    }

    public function updateStatus(int $id, int $status): bool
    {
        return $this->reviewRepo->updateStatus($id, $status);
    }

    public function create(array $data): Review
    {
        return $this->reviewRepo->create($data);
    }

    public function delete(int|array $id): int
    {
        return $this->reviewRepo->delete($id);
    }

    public function restore(int|array $ids): int
    {
        return $this->reviewRepo->restore($ids);
    }

    public function getTrashed(Request $request): LengthAwarePaginator
    {
        $perPage = Constants::DEFAULT_PER_PAGE;
        $dataSearch['keywords'] = $request->get('keywords', '');
        $dataSearch['status'] = $request->get('status', '');
        $dataSearch['rating'] = $request->get('rating', '');
        $relation = ['user', 'book'];
        $searchColumns = ['description', 'book.name', 'user.name'];

        return $this->reviewRepo->getTrashed($perPage, $relation, $dataSearch, $searchColumns);
    }
}
