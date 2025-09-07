<?php 

namespace App\Services\News\Sources;

use App\Services\News\Contracts\NewsSourceInterface;
use Illuminate\Support\Facades\Http;

class NewsApiSource implements NewsSourceInterface
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.newsapi.key'); // your API key
        $this->baseUrl = 'https://newsapi.org/v2';
    }

    public function fetchLatest(): array
    {
        $resp = Http::withHeaders([
            'Accept' => 'application/json'
        ])->get("{$this->baseUrl}/top-headlines", [
            'apiKey' => $this->apiKey,
            'language' => 'en',
            'pageSize' => 50,
        ]);

        if (!$resp->successful()) {
            throw new \RuntimeException('NewsAPI error: ' . $resp->body());
        }

        $data = $resp->json();
        $items = [];

        foreach ($data['articles'] ?? [] as $a) {
            $items[] = [
                'source_name' => 'newsapi',
                'source_id' => $a['source']['id'] ?? null,
                'title' => $a['title'] ?? null,
                'author' => $a['author'] ?? null,
                'summary' => $a['description'] ?? null,
                'content' => $a['content'] ?? null,
                'url' => $a['url'] ?? null,
                'image_url' => $a['urlToImage'] ?? null,
                'category' => null,
                'published_at' => isset($a['publishedAt']) ? date('Y-m-d H:i:s', strtotime($a['publishedAt'])) : null,
                'language' => 'en',
            ];
        }

        return $items;
    }
}
