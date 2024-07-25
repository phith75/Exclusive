<?php

namespace App\Services;

use App\Helpers\ImageHelper;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Utils\Constants;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;

class CategoryService
{
    protected $categoryRepo;

    public function __construct(CategoryRepository $categoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
    }

    public function getAll(): Collection
    {
        return $this->categoryRepo->getAll();
    }

    public function getPaginate(Request $request): LengthAwarePaginator
    {
        $keyword = $request->get('keywords', '');
        $perPage = Constants::DEFAULT_PER_PAGE;
        $relation = [];
        $dataSearch = [
            'keywords' => $keyword,
        ];
        $searchColumns = ['name', 'slug'];

        return $this->categoryRepo->searchAndPaginate($perPage, $relation, $dataSearch, $searchColumns);
    }

    public function find(int $id): ?object
    {
        return $this->categoryRepo->find($id);
    }

    public function create(array $data): Category
    {
        if (isset($data['image'])) {
            $data['image'] = ImageHelper::uploadAndResize($data['image'], 'public', null, null);
        }

        return $this->categoryRepo->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $category = $this->categoryRepo->find($id);

        if (isset($data['image'])) {
            $oldImage = $category->image;
            $data['image'] = ImageHelper::uploadAndResize($data['image'], 'public', null, null, $oldImage);
        }

        return $this->categoryRepo->update($id, $data);
    }

    public function delete(int|array $id): int
    {
        $inBook = $this->categoryRepo->inBook($id);
        if ($inBook) {
            return 0;
        }
        return $this->categoryRepo->delete($id);
    }

    public function getTrashed(Request $request): LengthAwarePaginator
    {
        $keyword = $request->get('keywords', '');
        $perPage = Constants::DEFAULT_PER_PAGE;
        $relation = [];
        $dataSearch = [
            'keywords' => $keyword,
        ];
        $searchColumns = ['name', 'slug'];

        return $this->categoryRepo->getTrashed($perPage, $relation, $dataSearch, $searchColumns);
    }

    public function restore(int|array $ids): int
    {
        return $this->categoryRepo->restore($ids);
    }
}
