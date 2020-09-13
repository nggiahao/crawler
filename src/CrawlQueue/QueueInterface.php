<?php

namespace Nggiahao\Crawler\CrawlQueue;

use Nggiahao\Crawler\CrawlUrl;

interface QueueInterface
{
    public function reset($site, $keep_data = false);

    public function resume($site);

    public function push(CrawlUrl $url);

    public function exists($url): bool;

    public function findByUrl($url, $site = null);

    public function hasPendingUrls($sites): bool;

    public function firstPendingUrl($sites): ?CrawlUrl;

    public function delay(CrawlUrl $crawlUrl);

    public function updateData( CrawlUrl $crawlUrl );

    public function insert(array $data);
}
