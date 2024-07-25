<?php

namespace App\Repositories;

use App\Models\Staff;
use App\Models\User;

class UserRepository extends BaseRepository
{
    protected $model;

    public function getModel(): string
    {
        return User::class;
    }

    public function updateStatus(int $id, int $status): bool
    {
        $user = $this->model->findOrFail($id);

        return $user->update(['status' => $status]);
    }

    public function updateRole(int $id, int $role): bool
    {
        $user = $this->model->findOrFail($id);

        return $user->update(['role' => $role]);
    }

    public function staffList()
    {
        return Staff::all();
    }
}
