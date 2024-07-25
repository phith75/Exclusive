<?php

namespace App\Services;

use App\Helpers\ImageHelper;
use App\Models\Book;
use App\Repositories\BookRepository;
use App\Utils\Constants;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;

class BookService
{
    protected $bookRepo;

    public function __construct(BookRepository $bookRepo)
    {
        $this->bookRepo = $bookRepo;
    }

    public function getAll(): Collection
    {
        return $this->bookRepo->getAll();
    }

    public function getWithRelation(Request $request): LengthAwarePaginator
    {
        $perPage = $request->get('per_page', Constants::DEFAULT_PER_PAGE);
        $dataSearch['keywords'] = $request->get('keywords', '');
        $dataSearch['author_id'] = $request->get('author_id', '');
        $dataSearch['publisher_id'] = $request->get('publisher_id', '');
        $dataSearch['category_id'] = $request->get('category_id', '');
        $searchColumns = ['name', 'description', 'short_description', 'slug'];
        $relation = ['category', 'author', 'publisher', 'bookImage', 'wishlists', 'reviews'];
        $books = $this->bookRepo->searchAndPaginate($perPage, $relation, $dataSearch, $searchColumns);

        return $books;
    }

    public function find(int $id): ?object
    {
        return $this->bookRepo->find($id);
    }

    public function findMultipleBooks(array $bookData): bool
    {       
        foreach ($bookData as $book) {
            $foundBook = $this->bookRepo->find($book['book_id']);
            if (!$foundBook) {
                return false;
            }
        }

        return true;
    }

    public function create(array $data): Book
    {
        $directory = 'public';
        $validatedData = $data;
        $images = $data['images'] ?? [];
        $thumbnailPath = ImageHelper::uploadAndResize($data['thumbnail'], $directory);
        $validatedData['thumbnail'] = $thumbnailPath;
        $book = $this->bookRepo->create($validatedData);
        if (!empty($images)) {
            foreach ($images as $image) {
                $imagePath = ImageHelper::uploadAndResize($image, $directory, null, null);
                $book->bookImage()->create(['image' => $imagePath]);
            }
        }

        return $book;
    }

    public function update(int $id, array $validatedData): bool
    {
        $directory = 'public';
        $book = $this->find($id);
        $oldImages = $validatedData['old_images'] ?? [];
        
        $existingImages = $this->bookRepo->getBookImageByBookId($id);
        $existingImagesToDelete = $existingImages->filter(function ($images) use ($oldImages) {
            return !in_array((string)$images->image, $oldImages);
        });

        foreach ($existingImagesToDelete as $imageToDelete) {
            ImageHelper::delete($directory, $imageToDelete->image);
            $imageToDelete->forceDelete();
        }

        // Handle new images
        if (isset($validatedData['images'])) {
            foreach ($validatedData['images'] as $image) {
                if (!$existingImages->contains('image', $image)) {
                    $imagePath = ImageHelper::uploadAndResize($image, $directory);
                    $book->bookImage()->create(['image' => $imagePath]);
                }
            }
        }

        // Handle thumbnail
        if (isset($validatedData['thumbnail'])) {
            ImageHelper::delete($directory, $book->thumbnail);
            $validatedData['thumbnail'] = ImageHelper::uploadAndResize($validatedData['thumbnail'], $directory);
        }

        return $this->bookRepo->update($id, $validatedData);
    }


    public function delete(int|array $id): int
    {
        $lendTicketDetail = $this->bookRepo->getLendTicketDetailByBookId($id);
        if (!empty($lendTicketDetail)) {
            return 0;
        }

        return $this->bookRepo->delete($id);
    }

    public function getTrashed(Request $request): LengthAwarePaginator
    {
        $perPage = $request->get('per_page', Constants::DEFAULT_PER_PAGE);
        $dataSearch['keywords'] = $request->get('keywords', '');
        $dataSearch['author_id'] = $request->get('author_id', '');
        $dataSearch['publisher_id'] = $request->get('publisher_id', '');
        $dataSearch['category_id'] = $request->get('category_id', '');
        $searchColumns = ['name', 'description', 'short_description', 'slug'];
        $relation = ['category', 'author', 'publisher', 'bookImage', 'wishlists', 'reviews'];
        $books = $this->bookRepo->getTrashed($perPage, $relation, $dataSearch, $searchColumns);

        return $books;
    }

    public function restore(int|array $ids): int
    {
        return $this->bookRepo->restore($ids);
    }

    public function getMostBorrowed(int $limit = Constants::LIMIT_QUANTITY): Collection
    {
        return $this->bookRepo->getMostBorrowed($limit);
    }

    public function incrementBorrowedCount(int $bookId, int $quantity): bool
    {
        $book = $this->bookRepo->find($bookId);
        if ($book) {
            $book->borrowed_count += $quantity;

            return $book->save();
        }

        return false;
    }
}
