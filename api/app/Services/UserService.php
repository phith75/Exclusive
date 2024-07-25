<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Utils\Constants;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;

class UserService
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function getAll(): Collection
    {
        return $this->userRepo->getAll();
    }

    public function getWithRelation(Request $request): LengthAwarePaginator
    {
        $perPage = $request->get('per_page', Constants::DEFAULT_PER_PAGE);
        $dataSearch['keywords'] = $request->get('keywords', '');
        $dataSearch['status'] = $request->get('status', '');
        $dataSearch['gender'] = $request->get('gender', '');
        $dataSearch['role'] = $request->get('role', '');
        $relation = ['province', 'district', 'ward', 'wishlists', 'reviews'];
        $searchColumn = ['name', 'email', 'phone', 'user_code','status','gender','role'];

        return $this->userRepo->searchAndPaginate($perPage, $relation, $dataSearch, $searchColumn);
    }

    public function find(int $id): ?object
    {
        return $this->userRepo->find($id);
    }

    public function create(array $data): User
    {
        return $this->userRepo->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->userRepo->update($id, $data);
    }
    //change status user

    public function updateStatus(int $id, int $status): bool
    {
        return $this->userRepo->updateStatus($id, $status);
    }

    public function updateRole(int $id, int $role): bool
    {
        return $this->userRepo->updateRole($id, $role);
    }

    public function delete(int|array $id): int
    {
        return $this->userRepo->delete($id);
    }

    public function getTrashed(int $perPage, string $keyword = ''): LengthAwarePaginator
    {
        return $this->userRepo->getTrashed($perPage, $keyword);
    }

    public function restore(int|array $ids): int
    {
        return $this->userRepo->restore($ids);
    }

    public function staffList()
    {
        return $this->userRepo->staffList();
    }
}
