<?php

namespace App\Console\Commands;

use App\Services\News\Sources\NewsApiSource;
use App\Models\Article;
use Illuminate\Console\Command;

class FetchNewsApi extends Command
{
    protected $signature = 'fetch:newsapi {--force : ignore rate limits}';
    protected $description = 'Fetch latest articles from NewsAPI and store them in database';

    public function handle()
    {
        $this->info('Fetching articles from NewsAPI...');

        $source = new NewsApiSource();

        try {
            $articles = $source->fetchLatest();
            $this->info("Fetched " . count($articles) . " articles");

            $stored = 0;
            $updated = 0;

            foreach ($articles as $item) {
                if (empty($item['url']) || empty($item['title'])) {
                    continue;
                }

                // Simplified with updateOrCreate
                $article = Article::updateOrCreate(
                    [
                        'source_name' => $item['source_name'],
                        'source_id' => $item['source_id'],
                    ],
                    $item
                );

                $article->wasRecentlyCreated ? $stored++ : $updated++;
            }

            $this->info("Stored: {$stored}, Updated: {$updated}");
        } catch (\Throwable $e) {
            $this->error('Error fetching NewsAPI: ' . $e->getMessage());
        }

        $this->info('Done fetching NewsAPI articles.');
        return 0;
    }
}
