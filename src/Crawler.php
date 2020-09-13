<?php

namespace Nggiahao\Crawler;

use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Arr;
use Nggiahao\Crawler\CrawlQueue\DatabaseQueue;
use Nggiahao\Crawler\Enum\CrawlStatus;
use Nggiahao\Crawler\SitesConfig\SiteInterface;
use Nggiahao\Crawler\SitesConfig\SiteManager;
use Vuh\CliEcho\CliEcho;
use function Amp\Parallel\Worker\enqueue;
use function Amp\Promise\all;
use function Amp\Promise\wait;

class Crawler
{
    /** @var SiteManager  */
    protected $site_manager;

    /** @var DatabaseQueue */
    protected $queue;

    protected $config = [
        'concurrency' => 10,
        'proxy'       => null,
        'browser'     => 'guzzle',
    ];

    public function __construct(SiteManager $site_manager, DatabaseQueue $queue) {
        $this->site_manager = $site_manager;
        $this->queue = $queue;
    }

    public function getConfig($key, $default = null) {
        return Arr::get($this->config, $key, $default);
    }

    /**
     * @param $sites
     * @param array $config
     * @param false $reset
     *
     * @throws SitesConfig\SiteNotFoundException|\Throwable
     */
    public function run($sites, array $config = [], $reset = false) {
        $sites = $this->getSitesConfig($sites);
        $this->config = array_merge($config, $this->config);

        $this->init($sites, $reset);

        $round_count = 0;
        $site_selector = $this->roundRobin($sites);

        while ($this->queue->hasPendingUrls($sites)) {
            $promises = [];
            for ($i = 0; $i < $this->getConfig('concurrency', 10); $i++) {
                $crawl_url = $this->queue->firstPendingUrl($site_selector->current());
                $site_selector->next();

                if (!$crawl_url) break;

                $promise = enqueue( new Task( $crawl_url, $this->getConfig('proxy'), $this->getConfig('browser')) );
                $promise->onResolve(function($ex, $result){
                    /** @var CrawlUrl $crawlUrl */
                    [ $crawlUrl, $urls ] = $result;
                    if ($crawlUrl->getStatus() == CrawlStatus::CRAWL_INIT) {
                        $this->queue->delay( $crawlUrl );
                        return;
                    }else {
//                        dump($crawlUrl);
                        $this->queue->changeProcessStatus( $crawlUrl, $crawlUrl->getStatus() );
                    }

                    $site = $crawlUrl->getSite();
                    foreach ($urls as $url){
                        if(!$site->shouldCrawl( $url )){
                            continue;
                        }
                        $crawl_url = CrawlUrl::create( $site, new Uri($url), $crawlUrl->url );
                        $this->queue->push( $crawl_url );
                    }

                });

                $promises[$crawl_url->getId()] = $promise;
            }
            wait(all($promises));

            $round_count++;
            CliEcho::warningnl("============================$round_count============================");
        }

    }

    /**
     * @param $sites
     *
     * @return array
     * @throws SitesConfig\SiteNotFoundException
     */
    protected function getSitesConfig($sites) {
        $sites = array_unique(Arr::wrap($sites));

        $sites_config = [];

        foreach ($sites as $site) {
            $sites_config[] = $this->site_manager->getSiteConfig($site);
        }

        return $sites_config;
    }

    public function init(array $sites, bool $reset) {
        /** @var SiteInterface $site */
        foreach ($sites as $site) {
            if ($reset) {
                $this->queue->reset( (string)$site );
            }
            foreach ($site->startUrls() as $url) {
                $crawl_url = CrawlUrl::create($site, new Uri($url));
                if ($this->queue->push($crawl_url)) {
                    CliEcho::successnl("[$site] Added $crawl_url->url");
                }
            }
        }
    }

    protected function roundRobin($array){
        $count = count($array);
        $index = 0;
        while(1){
            yield $array[$index];
            $index++;
            if($index == $count){
                $index = 0;
            }
        }
    }
}
