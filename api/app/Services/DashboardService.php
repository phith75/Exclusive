<?php

namespace App\Services;

use App\Repositories\DashboardRepository;

class DashboardService
{
    protected $dashboardRepository;

    public function __construct(DashboardRepository $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    public function getStatusData(): array
    {
        return [
            'total_users' => $this->dashboardRepository->getTotalUsers(),
            'total_books' => $this->dashboardRepository->getTotalBooks(),
            'total_lend_tickets' => $this->dashboardRepository->getTotalLendTickets(),
            'total_books_available' => $this->dashboardRepository->getTotalBooksAvailable(),
        ];
    }

    public function getTopCustomersBorrowingBooks(?string $interval = null, ?string $startDate = null, ?string $endDate = null, int $quantity = 10): array
    {
        return $this->dashboardRepository->getTopCustomersBorrowingBooks($interval, $startDate, $endDate, $quantity);
    }

    public function getTopBookborrowedtheMost(?string $interval = null, ?string $startDate = null, ?string $endDate = null, int $quantity = 10): array
    {
        return $this->dashboardRepository->getTopBookborrowedtheMost($interval, $startDate, $endDate, $quantity);
    }

    public function getMonthlyBorrowedBooks(): array
    {
        return $this->dashboardRepository->getMonthlyBorrowedBooks();
    }

    public function getBooksByCategory(?string $interval = null, ?string $startDate = null, ?string $endDate = null): array
    {
        return $this->dashboardRepository->getBooksByCategory($interval, $startDate, $endDate);
    }

    public function getTop30FavoriteBooks(?string $interval = null, ?string $startDate = null, ?string $endDate = null): array
    {
        return $this->dashboardRepository->getTop30FavoriteBooks($interval, $startDate, $endDate);
    }
}
