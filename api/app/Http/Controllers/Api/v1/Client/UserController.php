<?php

namespace App\Http\Controllers\Api\v1\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

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
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->userService->find($id);

        return $this->responseSuccess(UserResource::make($user));
    }
}
