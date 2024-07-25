<?php

namespace App\Console\Commands;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Faker\Factory;
use Http;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CrawlBookData extends Command
{
    const SOURCE_URL = 'https://www.googleapis.com/books/v1/volumes';
    const IMAGE_URL = 'https://books.google.com/books/publisher/content/images/frontcover/{id}?fife=w480-h640&source=gbs_api';

    // JSON Paths
    const TITLE_PATH = 'volumeInfo.title';
    const AUTHORS_PATH = 'volumeInfo.authors';
    const PUBLISHER_PATH = 'volumeInfo.publisher';
    const PUBLISHED_DATE_PATH = 'volumeInfo.publishedDate';
    const DESCRIPTION_PATH = 'volumeInfo.description';
    const PAGE_COUNT_PATH = 'volumeInfo.pageCount';
    const CATEGORIES_PATH = 'volumeInfo.categories';
    const THUMBNAIL_PATH = 'volumeInfo.imageLinks.thumbnail';
    const PRICE_PATH = 'saleInfo.listPrice.amount';

    protected $signature = 'crawl:book-info {--q=a} {--startIndex=0} {--endIndex=} {--maxResults=40}';
    protected $description = 'Crawl book information from Google Books API';

    public function handle()
    {
        $this->warn('Start crawling book info');
        $faker = Factory::create();
        $endIndex = $this->option('endIndex') ? $this->option('endIndex') * $this->option('maxResults') : ($this->option('startIndex') + 20) * $this->option('maxResults');

        for ($i = $this->option('startIndex'); $i < $endIndex; $i += $this->option('maxResults')) {
            $this->info('Crawling page: ' . $i);

            $response = Http::get(self::SOURCE_URL, [
                'q' => $this->option('q'),
                'startIndex' => $i,
                'maxResults' => $this->option('maxResults'),
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (empty($data['items'])) {
                $this->warn('No more books found');
                $this->warn('End crawling book info');

                return;
            }

            foreach ($data['items'] as $detail) {
                if (! $this->hasRequiredInfo($detail)) {
                    $this->warn('Missing info: ' . data_get($detail, self::TITLE_PATH));
                    continue;
                }

                $publisher = $this->firstOrCreatePublisher(data_get($detail, self::PUBLISHER_PATH));
                $author = $this->firstOrCreateAuthors(data_get($detail, self::AUTHORS_PATH));
                $category = $this->firstOrCreateCategories(data_get($detail, self::CATEGORIES_PATH), $faker);

                if (Book::where('name', data_get($detail, self::TITLE_PATH))->exists()) {
                    $this->warn('Book already exists: ' . data_get($detail, self::TITLE_PATH));
                    continue;
                }

                $this->info('  Crawling book: ' . data_get($detail, self::TITLE_PATH));

                $book = new Book([
                    'name' => data_get($detail, self::TITLE_PATH),
                    'slug' => Str::slug(data_get($detail, self::TITLE_PATH)),
                    'description' => data_get($detail, self::DESCRIPTION_PATH) ?? $faker->paragraphs(4, true),
                    'short_description' => $this->getFirstTwoSentencesRegex(data_get($detail, self::DESCRIPTION_PATH) ?? $faker->sentence(10)),
                    'thumbnail' => str_replace('{id}', data_get($detail, 'id'), self::IMAGE_URL),
                    'quantity' => rand(1, 20),
                    'publisher_id' => Publisher::inRandomOrder()->first()->id,
                    'category_id' => Category::inRandomOrder()->first()->id,
                    'author_id' => Author::inRandomOrder()->first()->id,
                ]);

                $book->save();

                for ($i = 0; $i < rand(4, 7); $i++) {
                    $book->bookImage()->create([
                        'book_id' => $book->id,
                        'image' => $faker->imageUrl(480, 640),
                    ]);
                }

                $this->warn('Crawled book: ' . data_get($detail, self::TITLE_PATH));
            }
        }

        $this->warn('End crawling book info');
    }

    private function hasRequiredInfo($detail)
    {
        return data_get($detail, self::CATEGORIES_PATH) && data_get($detail, self::THUMBNAIL_PATH) && data_get($detail, self::AUTHORS_PATH) && data_get($detail, self::PUBLISHER_PATH);
    }

    private function firstOrCreatePublisher($publisherName)
    {
        try {
            return Publisher::firstOrCreate(['name' => $publisherName]);
        } catch (\Exception) {
            return Publisher::where('slug', Str::slug($publisherName))->first();
        }
    }

    private function firstOrCreateAuthors($authors)
    {
        return collect($authors)->map(function ($author) {
            try {
                return Author::firstOrCreate(['name' => $author]);
            } catch (\Exception) {
                return Author::where('slug', Str::slug($author))->first();
            }
        });
    }

    private function firstOrCreateCategories($categories, $faker)
    {
        return collect($categories)->map(function ($category) use ($faker) {
            return Category::firstOrCreate([
                'name' => $category,
            ], [
                'image' => $faker->imageUrl(56, 56),
                'description' => $faker->sentence,
            ]);
        });
    }

    private function getFirstTwoSentencesRegex($text = '')
    {
        preg_match('/^([^.]+\.){1,2}([^.]*)/', $text, $matches);

        return $matches[0] ?? '';
    }
}
