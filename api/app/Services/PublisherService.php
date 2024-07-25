<?php

namespace App\Services;

use App\Models\Publisher;
use App\Repositories\PublisherRepository;
use App\Utils\Constants;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;

class PublisherService
{
    protected $publisherRepo;

    public function __construct(PublisherRepository $publisherRepo)
    {
        $this->publisherRepo = $publisherRepo;
    }

    public function getAll(): Collection
    {
        return $this->publisherRepo->getAll();
    }

    public function getPaginate(Request $request): LengthAwarePaginator
    {
        $perPage = Constants::DEFAULT_PER_PAGE;
        $dataSearch['keywords'] = $request->get('keywords', '');
        $relation = [];
        $searchColumns = ['name', 'slug'];

        return $this->publisherRepo->searchAndPaginate($perPage, $relation, $dataSearch, $searchColumns);
    }

    public function find(int $id): ?object
    {
        return $this->publisherRepo->find($id);
    }

    public function create(array $data): Publisher
    {
        return $this->publisherRepo->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->publisherRepo->update($id, $data);
    }

    public function delete(int|array $id): int
    {
        $inBook = $this->publisherRepo->inBook($id);
        if ($inBook) {
            return 0;
        }
        return $this->publisherRepo->delete($id);
    }

    public function getTrashed(Request $request): LengthAwarePaginator
    {
        $perPage = Constants::DEFAULT_PER_PAGE;
        $dataSearch['keywords'] = $request->get('keywords', '');
        $relation = [];
        $searchColumns = ['name', 'slug'];
        return $this->publisherRepo->getTrashed($perPage, $relation, $dataSearch, $searchColumns);
    }

    public function restore(int|array $ids): int
    {
        return $this->publisherRepo->restore($ids);
    }
}
