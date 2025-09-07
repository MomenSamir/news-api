<?php 


namespace App\Services\News\Contracts;

interface NewsSourceInterface
{
    /**
     * Fetch latest articles (raw array) from the source.
     * Return an array of normalized articles matching Article::create() keys.
     *
     * @return array
     */
    public function fetchLatest(): array;
}
