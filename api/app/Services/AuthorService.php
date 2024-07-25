<?php

namespace App\Services;

use App\Models\Author;
use App\Repositories\AuthorRepository;
use App\Utils\Constants;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;

class AuthorService
{
    protected $authorRepo;

    public function __construct(AuthorRepository $authorRepo)
    {
        $this->authorRepo = $authorRepo;
    }

    public function getAll(): Collection
    {
        return $this->authorRepo->getAll();
    }

    public function getPaginate(Request $request): LengthAwarePaginator
    {
        $perPage = Constants::DEFAULT_PER_PAGE;
        $dataSearch['keywords'] = $request->get('keywords', '');
        $searchColumns = ['name', 'slug'];
        $relation = [];
        $authors = $this->authorRepo->searchAndPaginate($perPage, $relation, $dataSearch, $searchColumns);

        return $authors;
    }

    public function find(int $id): ?object
    {
        return $this->authorRepo->find($id);
    }

    public function create(array $data): Author
    {
        return $this->authorRepo->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->authorRepo->update($id, $data);
    }

    public function delete(int|array $id): int
    {
        $inBook = $this->authorRepo->inBook($id);
        if ($inBook) {
            return 0;
        }
        return $this->authorRepo->delete($id);
    }

    public function getTrashed(Request $request): LengthAwarePaginator
    {
        $perPage = Constants::DEFAULT_PER_PAGE;
        $dataSearch['keywords'] = $request->get('keywords', '');
        $searchColumns = ['name', 'slug'];
        $relation = [];
        return $this->authorRepo->getTrashed($perPage, $relation, $dataSearch, $searchColumns);
    }

    public function restore(int|array $ids): int
    {
        return $this->authorRepo->restore($ids);
    }
}
