<?php


namespace Nggiahao\Crawler;


use Amp\Parallel\Worker\Environment;
use Nggiahao\Crawler\Browsers\BrowserManager;
use Nggiahao\Crawler\Enum\CrawlStatus;
use Nggiahao\Crawler\Helpers\PhpUri;
use Symfony\Component\DomCrawler\Crawler;
use Vuh\CliEcho\CliEcho;

class Task implements \Amp\Parallel\Worker\Task {
    
    /** @var CrawlUrl */
    protected $crawlUrl;

    protected $proxy;

    protected $browser;

    /**
     * Task constructor.
     *
     * @param CrawlUrl $crawlUrl
     * @param null $proxy
     * @param string $browser
     */
    public function __construct( CrawlUrl $crawlUrl, $proxy = null, $browser = 'guzzle' ) {
        $this->crawlUrl = $crawlUrl;
        $this->proxy = $proxy;
        $this->browser = $browser;
    }
    
    
    /**
     * Runs the task inside the caller's context.
     *
     * Does not have to be a coroutine, can also be a regular function returning a value.
     *
     * @param \Amp\Parallel\Worker\Environment
     *
     * @return mixed|\Amp\Promise|\Generator
     */
    public function run( Environment $environment ) {
    
        try{
            CliEcho::warningnl("Processing [{$this->crawlUrl->url}]");

            $site = $this->crawlUrl->getSite();

            if ($this->proxy) {
                BrowserManager::setProxies($this->proxy);
            }
            $html = BrowserManager::get($this->browser)->getHtml( $this->crawlUrl->url );

            $crawler = new Crawler();
            $crawler->addHtmlContent( $html );
            $urls_selector = $crawler->filter( 'a');

            $urls = [];
            /** @var \DOMElement $item */
            foreach ($urls_selector as $item) {
                $item = $item->getAttribute('href');
                $item = PhpUri::parse($site->rootUrl())->join($item);
                if ($site->shouldCrawl($item)) {
                    $urls[] = $item;
                }
            }
            $urls = array_unique($urls);
            // parse data
            if($site->shouldGetData( $this->crawlUrl->url )){
                $data = $site->getInfoFromCrawler($crawler);
                $this->crawlUrl->setData( $data );
            }
            $this->crawlUrl->setStatus( CrawlStatus::CRAWL_DONE);
            return [
                $this->crawlUrl,
                $urls,
            ];
        }catch (\Exception $ex){
            CliEcho::errornl($ex->getMessage());
            if($ex->getCode() == 429){
                $this->crawlUrl->setStatus( CrawlStatus::CRAWL_INIT );
            } else{
                $this->crawlUrl->setStatus( CrawlStatus::CRAWL_FAIL );
            }
            return [
                $this->crawlUrl,
                [],
            ];
        }
    }
}
