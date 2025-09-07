<?php
namespace App\Providers;

use App\Services\News\NewsAggregator;
use App\Services\News\Sources\GuardianSource;
use App\Services\News\Sources\NewsApiSource;
use App\Services\News\Sources\NytSource;
use Illuminate\Support\ServiceProvider;

class NewsServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Nothing here
    }

    public function boot()
    {
        $this->app->singleton(NewsAggregator::class, function ($app) {
            $aggregator = new NewsAggregator();

            // Add the adapters (they use config('services.*'))
            $aggregator->addSource(new NewsApiSource());
            $aggregator->addSource(new GuardianSource());
            $aggregator->addSource(new NytSource());

            return $aggregator;
        });
    }
}
