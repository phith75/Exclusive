<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Enum\UserRole;
use App\Enum\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Imports\StaffImport;
use App\Services\UserService;
use App\Utils\Constants;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    protected $userService;

    protected $roleService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $users = $this->userService->getWithRelation($request);

        return $this->responseSuccessPaginate(UserResource::collection($users));
    }

    public function getAll(): JsonResponse
    {
        $users = $this->userService->getAll();

        return $this->responseSuccess(UserResource::collection($users));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $newUser = $this->userService->create($validatedData);

        return $this->responseSuccess($newUser, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->userService->find($id);

        return $this->responseSuccess(UserResource::make($user));
    }

    public function updateStatus(int $id, Request $request): JsonResponse
    {
        $validatedData = $request->validate(
            [
                'status' => 'required|in:' . implode(',', UserStatus::values()),
            ]
        );
        $status = $this->userService->updateStatus($id, $validatedData['status']);

        return $this->responseSuccess($status);
    }

    public function updateRole(int $id, Request $request): JsonResponse
    {
        $validatedData = $request->validate(
            [
                'role' => 'required|in:' . implode(',', UserRole::values()),
            ]
        );
        $role = $this->userService->updateRole($id, $validatedData['role']);

        return $this->responseSuccess($role);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->userService->delete($id);

        return $this->responseSuccess('User deleted successfully');
    }

    /**
     * Get a list of soft-deleted users.
     */
    public function getTrashed(UserRequest $request): JsonResponse
    {
        $perPage = $request->get('per_page', Constants::DEFAULT_PER_PAGE);
        $keywords = $request->get('keywords', '');
        $keywords = trim($keywords);
        $users = $this->userService->getTrashed($perPage, $keywords);

        return $this->responseSuccessPaginate(UserResource::collection($users));
    }

    public function restore(UserRequest $request): JsonResponse
    {
        $restoredUser = $this->userService->restore($request->ids);

        return $this->responseSuccess($restoredUser);
    }

    public function import(Request $request)
    {
        $validatedData = $request->validate([
            'user_file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        $import = new StaffImport();
        Excel::import($import, $validatedData['user_file']);
        $importCount = $import->getImportCount();

        return $this->responseSuccess($importCount);
    }

    public function deleteMany(UserRequest $request): JsonResponse
    {
        $deleted = $this->userService->delete($request->ids);

        return $this->responseSuccess($deleted);
    }

    public function staffList(): JsonResponse
    {
        $staffs = $this->userService->staffList();

        return $this->responseSuccess($staffs);
    }
}
