<?php

namespace App\Services\News;

use App\Models\Article;
use Illuminate\Support\Facades\Log;

class NewsAggregator
{
    /** @var \App\Services\News\Contracts\NewsSourceInterface[] */
    protected $sources = [];

    public function __construct(iterable $sources = [])
    {
        $this->sources = $sources;
    }

    /**
     * Add a source
     */
    public function addSource($source)
    {
        $this->sources[] = $source;
    }

    /**
     * Get all sources
     *
     * @return array
     */
    public function getSources(): array
    {
        return $this->sources;
    }

    /**
     * Fetch and store articles from all sources
     */
    public function fetchAndStore(): array
    {
        return $this->fetchFromSource();
    }

    /**
     * Fetch from a specific source (by class name or custom getName() method)
     */
    public function fetchFromSource(string $sourceName = null): array
    {
        $results = [
            'fetched' => 0,
            'stored' => 0,
            'updated' => 0,
            'error' => null,
        ];

        foreach ($this->sources as $source) {
            $name = method_exists($source, 'getName') ? $source->getName() : class_basename($source);

            // Skip if filtering by specific source
            if ($sourceName && strtolower($name) !== strtolower($sourceName)) {
                continue;
            }

            try {
                $items = $source->fetchLatest();
                $results['fetched'] += count($items);

                foreach ($items as $item) {
                    if (empty($item['url']) || empty($item['title'])) {
                        continue;
                    }

                    $existing = Article::where('url', $item['url'])->first();

                    if ($existing) {
                        $existing->fill($item);
                        $existing->save();
                        $results['updated']++;
                    } else {
                        Article::create($item);
                        $results['stored']++;
                    }
                }
            } catch (\Throwable $e) {
                Log::error("Source fetch error ({$name}): " . $e->getMessage());
                $results['error'] = $e->getMessage();
            }

            // If specific source was requested, stop after first
            if ($sourceName) {
                break;
            }
        }

        return $results;
    }
}
