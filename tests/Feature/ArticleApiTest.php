<?php
namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_articles_with_filters()
    {
        Article::factory()->create([
            'title' => 'Laravel tips',
            'source_name' => 'newsapi',
            'published_at' => now()->subDay(),
        ]);
        Article::factory()->create([
            'title' => 'Guardian piece',
            'source_name' => 'the-guardian',
            'published_at' => now(),
        ]);

        $resp = $this->getJson('/api/articles?source=newsapi');
        $resp->assertStatus(200);
        $this->assertCount(1, $resp->json('data'));
    }

    public function test_get_single_article()
    {
        $a = Article::factory()->create();
        $resp = $this->getJson("/api/articles/{$a->id}");
        $resp->assertStatus(200)
             ->assertJsonFragment(['id' => $a->id]);
    }
}
