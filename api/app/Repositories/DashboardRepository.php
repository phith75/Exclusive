<?php

namespace App\Repositories;

use App\Enum\TicketDetailStatus;
use App\Models\Book;
use App\Models\Category;
use App\Models\LendTicket;
use App\Models\LendTicketDetail;
use App\Models\Publisher;
use App\Models\User;
use App\Utils\Constants;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    public function getTotalUsers(): int
    {
        return User::count();
    }

    public function getTotalBooks(): int
    {
        return Book::count();
    }

    public function getTotalLendTickets(): int
    {
        return LendTicket::count();
    }

    public function getTotalBooksAvailable(): int
    {
        return Book::where('quantity', '>', 0)->count();
    }

    public function getTopCustomersBorrowingBooks(string $interval = null, string $startDate = null, string $endDate = null, int $quantity = Constants::LIMIT_QUANTITY): array
    {
        return User::withCount(['lendTickets' => function ($query) use ($interval, $startDate, $endDate) {
            $this->applyDateIntervalOrRange($query, $interval, $startDate, $endDate, 'lend_tickets');
        }])
            ->orderBy('num_borrowed_books', 'desc')
            ->limit($quantity)
            ->get()
            ->toArray();
    }

    public function getTopBookborrowedtheMost(string $interval = null, string $startDate = null, string $endDate = null, int $quantity = Constants::LIMIT_QUANTITY): array

    {
        return Book::withCount(['lendTickets' => function ($query) use ($interval, $startDate, $endDate) {
            $this->applyDateIntervalOrRange($query, $interval, $startDate, $endDate, 'lend_tickets');
        }])
            ->orderBy('lend_tickets_count', 'desc')
            ->limit($quantity)
            ->get()
            ->toArray();
    }

    public function getMonthlyBorrowedBooks(): array
    {
        $currentMonth = date('n');
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        $query = LendTicket::select(
            DB::raw("MONTH(borrowed_date) as month"),
            DB::raw("YEAR(borrowed_date) as year"),
            DB::raw('COUNT(*) as borrow_count')
        )
            ->whereRaw('MONTH(borrowed_date) <= ?', [$currentMonth])
            ->groupBy('month', 'year')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc');

        $results = $query->get()->toArray();

        foreach ($results as &$result) {
            $result['month_name'] = $months[$result['month']];
        }

        return $results;
    }


    public function getBooksByCategory(string $interval = null, string $startDate = null, string $endDate = null): array
    {
        $query = Category::join('books', 'categories.id', '=', 'books.category_id')
            ->join('lend_ticket_details', 'books.id', '=', 'lend_ticket_details.book_id')
            ->select('categories.name', DB::raw('COUNT(books.id) as book_count'))
            ->groupBy('categories.name')
            ->orderBy('book_count', 'desc')
            ->limit(10);

        if ($interval || ($startDate && $endDate)) {
            $this->applyDateIntervalOrRange($query, $interval, $startDate, $endDate, 'lend_ticket_details');
        }

        return $query->get()->toArray();
    }

    public function getTop30FavoriteBooks(string $interval = null, string $startDate = null, string $endDate = null): array
    {
        $query = Book::withCount(['wishlists as wishlists_count' => function ($query) use ($interval, $startDate, $endDate) {
            $this->applyDateIntervalOrRange($query, $interval, $startDate, $endDate, 'wishlists');
        }])
            ->orderBy('wishlists_count', 'desc')
            ->limit(30);

        return $query->get(['name', 'wishlists_count'])->toArray();
    }


    private function applyDateIntervalOrRange($query, string $interval = null, string $startDate = null, string $endDate = null, string $tableName)
    {
        if ($interval) {
            switch ($interval) {
                case 'week':
                    $query->where("$tableName.created_at", '>=', Carbon::now()->subWeek());
                    break;
                case 'month':
                    $query->where("$tableName.created_at", '>=', Carbon::now()->subMonth());
                    break;
                case 'year':
                    $query->where("$tableName.created_at", '>=', Carbon::now()->subYear());
                    break;
            }
        } elseif ($startDate && $endDate) {
            $query->whereBetween("$tableName.created_at", [$startDate, $endDate]);
        }
    }
}
