<?php

namespace App\Services\News\Sources;

use App\Services\News\Contracts\NewsSourceInterface;
use Illuminate\Support\Facades\Http;

class GuardianSource implements NewsSourceInterface
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = 'bdad6f47-b7bd-4572-9261-93abdc3e413d';
        $this->baseUrl = 'https://content.guardianapis.com';
    }

    public function fetchLatest(): array
    {
        $resp = Http::get("{$this->baseUrl}/search", [
            'api-key' => $this->apiKey,
            'show-fields' => 'headline,body,byline,thumbnail',
            'page-size' => 50,
        ]);

        if (!$resp->successful()) {
            throw new \RuntimeException('Guardian API error: '.$resp->body());
        }

        $data = $resp->json();
        $items = [];

        foreach ($data['response']['results'] ?? [] as $a) {
            $fields = $a['fields'] ?? [];
            $items[] = [
                'source_name' => 'guardian',
                'source_id' => $a['id'] ?? null,
                'title' => $fields['headline'] ?? null,
                'author' => $fields['byline'] ?? null,
                'summary' => null,
                'content' => $fields['body'] ?? null,
                'url' => $a['webUrl'] ?? null,
                'image_url' => $fields['thumbnail'] ?? null,
                'category' => $a['sectionName'] ?? null,
                'published_at' => isset($a['webPublicationDate']) ? date('Y-m-d H:i:s', strtotime($a['webPublicationDate'])) : null,
                'language' => 'en',
            ];
        }

        return $items;
    }
}
