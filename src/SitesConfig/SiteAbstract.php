<?php


namespace Nggiahao\Crawler\SitesConfig;


use Symfony\Component\DomCrawler\Crawler;

abstract class SiteAbstract implements SiteInterface {
    
    final public function getName(): string {
        return strtolower( preg_replace( "/^.*\\\([^\\\]+)$/ui", "$1", get_class($this) ) );
    }
    
    public function maxDepth(): int {
        return -1;
    }
    
    public function delay(): int {
        return 0;
    }
    
    public function shouldCrawl( $url ) {
        return true;
    }
    
    public function shouldGetData( $url ) {
        return null;
    }

    public function getInfoFromCrawler( Crawler $dom_crawler ) {
        $title = $dom_crawler->filterXPath('//title')->text();

        return [
            'title' => $title,
        ];
    }

    public function __toString()
    {
        return $this->getName();
    }

}
