<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function getStatus()
    {
        $statusData = $this->dashboardService->getStatusData();

        return $this->responseSuccess($statusData);
    }

    public function getTopBookborrowedtheMost(Request $request, $interval = null)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $quantity = $request->get('quantity', 10);
        $topBooks = $this->dashboardService->getTopBookborrowedtheMost($interval, $startDate, $endDate, $quantity);

        return $this->responseSuccess($topBooks);
    }

    public function getTopCustomersBorrowingBooks(Request $request, $interval = null)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $quantity = $request->get('quantity', 10);
        $topCustomers = $this->dashboardService->getTopCustomersBorrowingBooks($interval, $startDate, $endDate, $quantity);

        return $this->responseSuccess($topCustomers);
    }

    public function getMonthlyBorrowedBooks()
    {
        $monthlyBorrowedBooks = $this->dashboardService->getMonthlyBorrowedBooks();

        return $this->responseSuccess($monthlyBorrowedBooks);
    }

    public function getBooksByCategory(Request $request)
    {
        $interval = $request->get('interval');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $booksByCategory = $this->dashboardService->getBooksByCategory($interval, $startDate, $endDate);

        return $this->responseSuccess($booksByCategory);
    }

    public function getTop30FavoriteBooks(Request $request)
    {
        $interval = $request->get('interval');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $top30FavoriteBooks = $this->dashboardService->getTop30FavoriteBooks($interval, $startDate, $endDate);

        return $this->responseSuccess($top30FavoriteBooks);
    }
}
