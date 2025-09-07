<?php

namespace App\Console\Commands;

use App\Services\News\Sources\GuardianSource;
use App\Models\Article;
use Illuminate\Console\Command;

class FetchGuardian extends Command
{
    protected $signature = 'fetch:guardian {--force : ignore rate limits}';
    protected $description = 'Fetch latest articles from Guardian and store them in database';

    public function handle()
    {
        $this->info('Fetching articles from Guardian...');

        $source = new GuardianSource();

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
                    ['url' => $item['url']],
                    $item
                );

                $article->wasRecentlyCreated ? $stored++ : $updated++;
            }

            $this->info("Stored: {$stored}, Updated: {$updated}");
        } catch (\Throwable $e) {
            $this->error('Error fetching Guardian: ' . $e->getMessage());
        }

        $this->info('Done fetching Guardian articles.');
        return 0;
    }
}
