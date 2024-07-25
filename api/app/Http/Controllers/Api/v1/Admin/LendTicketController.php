<?php

namespace App\Http\Controllers\Api\v1\Admin;

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
     * Store a newly created resource in storage.
     */
    public function store(LendTicketRequest $request): JsonResponse
    {
        $this->bookService->findMultipleBooks($request->get('book_data'));
        $newLendTicket = $this->lendTicketService->create($request->validated());

        return $this->responseSuccess($newLendTicket);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $lendTicket = $this->lendTicketService->find($id);

        return $this->responseSuccess(LendTicketResource::make($lendTicket));
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->lendTicketService->delete($id);

        return $this->responseSuccess('LendTicket deleted successfully');
    }
}
