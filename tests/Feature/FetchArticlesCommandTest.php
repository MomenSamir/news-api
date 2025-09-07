<?php
namespace Tests\Feature;

use App\Models\Article;
use App\Services\News\Sources\NewsApiSource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FetchArticlesCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_fetch_command_stores_articles()
    {
        // fake NewsAPI response
        Http::fake([
            'newsapi.org/*' => Http::response([
                'status' => 'ok',
                'totalResults' => 1,
                'articles' => [[
                    'source' => ['id' => 'bbc-news', 'name' => 'BBC News'],
                    'author' => 'Author',
                    'title' => 'Test Article',
                    'description' => 'desc',
                    'url' => 'https://example.com/test',
                    'urlToImage' => null,
                    'publishedAt' => now()->toIso8601String(),
                    'content' => 'content',
                ]]
            ], 200),
            // stub other providers with empty arrays/responses
            '*' => Http::response([], 200)
        ]);

        $this->artisan('fetch:articles')->assertExitCode(0);

        $this->assertDatabaseHas('articles', ['title' => 'Test Article', 'url' => 'https://example.com/test']);
    }
}
