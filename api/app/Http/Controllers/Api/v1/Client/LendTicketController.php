<?php

namespace App\Http\Controllers\Api\v1\Client;

use App\Enum\TicketDetailStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\LendTicketRequest;
use App\Http\Resources\LendTicketResource;
use App\Services\BookService;
use App\Services\LendTicketService;
use App\Services\TicketDetailService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class LendTicketController extends Controller
{
    protected $lendTicketService;

    protected $ticketDetailService;

    protected $userService;

    protected $bookService;

    public function __construct(
        LendTicketService $lendTicketService,
        UserService $userService,
        BookService $bookService,
        TicketDetailService $ticketDetailService
    ) {
        $this->lendTicketService = $lendTicketService;
        $this->ticketDetailService = $ticketDetailService;
        $this->bookService = $bookService;
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $lendTickets = $this->lendTicketService->getPaginate($request);

        return $this->responseSuccessPaginate(LendTicketResource::collection($lendTickets));
    }

    /**
     * Display the specified resource.
     */
    public function store(LendTicketRequest $request): JsonResponse
    {
        $this->bookService->findMultipleBooks($request->get('book_data'));

        $newLendTicket = $this->lendTicketService->create($request->validated());

        return $this->responseSuccess($newLendTicket);
    }

    public function show(int $id): JsonResponse
    {
        $lendTicket = $this->lendTicketService->find($id);

        return $this->responseSuccess(LendTicketResource::make($lendTicket));
    }

    public function findByUser(Request $request): JsonResponse
    {
        $id = auth()->user()->id;
        $lendTickets = $this->lendTicketService->findByUserId($id, $request);

        return $this->responseSuccess(LendTicketResource::collection($lendTickets));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $status = $request->status ? $request->status : TicketDetailStatus::BORROWED->value;
        $ticketDetail = $this->ticketDetailService->updateStatus($id, $status);

        return $this->responseSuccess($ticketDetail);
    }
}
