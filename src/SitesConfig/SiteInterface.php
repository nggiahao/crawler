<?php


namespace Nggiahao\Crawler\SitesConfig;

use Nggiahao\Crawler\CrawlUrl;
use Symfony\Component\DomCrawler\Crawler;

interface SiteInterface {

    public function getName() : string;
    public function rootUrl() : string;
    public function startUrls() : array ;
    public function maxDepth() : int;
    public function delay() : int;
    public function shouldCrawl($url);
    public function shouldGetData($url);
    public function getInfoFromCrawler( Crawler $dom_crawler );
    public function __toString();
}
