<?php

namespace Nggiahao\Crawler;

use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Arr;
use Nggiahao\Crawler\Enum\CrawlStatus;
use Nggiahao\Crawler\Enum\DataStatus;
use Nggiahao\Crawler\SitesConfig\SiteInterface;
use Nggiahao\Crawler\SitesConfig\SiteManager;
use Psr\Http\Message\UriInterface;

class CrawlUrl
{
    /** @var UriInterface */
    public $url;

    /** @var UriInterface */
    public $foundOnUrl;

    /** @var mixed */
    protected $id;

    protected $visited = 0;

    /** @var SiteInterface */
    protected $site;

    protected $has_data = DataStatus::DATA_INIT;

    protected $data = [];

    protected $status = CrawlStatus::CRAWL_INIT;

    /**
     * CrawlUrl constructor.
     *
     * @param SiteInterface $site
     * @param UriInterface $url
     * @param UriInterface|null $foundOnUrl
     */
    protected function __construct(SiteInterface $site, UriInterface $url, ?UriInterface $foundOnUrl = null)
    {
        $this->url = $url;
        $this->site = $site;
        $this->foundOnUrl = $foundOnUrl;
    }

    /**
     * @param SiteInterface $site
     * @param string|UriInterface $url
     * @param UriInterface|null $foundOnUrl
     * @param null $id
     *
     * @return static
     */
    public static function create( SiteInterface $site, UriInterface $url, ?UriInterface $foundOnUrl = null, $id = null)
    {
        $static = new static($site, $url, $foundOnUrl);

        if ($id !== null) {
            $static->setId($id);
        }

        return $static;
    }

    /**
     * @param $object
     *
     * @return static
     * @throws SitesConfig\SiteNotFoundException
     */
    public static function fromObject($object){
        $site = (new SiteManager())->getSiteConfig($object->site);
        $url = new Uri($object->url);
        $foundOnUrl = $object->parent ? new Uri($object->parent) : null;

        $instance = self::create( $site, $url, $foundOnUrl, $object->id );

        $instance->status = $object->status;
        $instance->data = \GuzzleHttp\json_decode( $object->data );
        $instance->has_data = $object->has_data;
        $instance->visited = $object->visited;
        
        return $instance;
    }
    
    public function toArray(){
        $data = [
            'url' => $this->url,
            'site' => $this->site,
            'url_hash' => hash_url( $this->url ),
            'parent' => $this->foundOnUrl,
            'status' => $this->status,
            'has_data' => $this->has_data,
            'data' => \GuzzleHttp\json_encode( $this->data ),
            'visited' => $this->visited,
        ];
        if($this->id){
            $data['id'] = $this->id;
        }
        return $data;
    }

    /**
     * @return mixed|null
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function foundOnUrl() {
        return $this->foundOnUrl;
    }

    public function getSite() {
        return $this->site;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    public function hasData() {
        return $this->has_data;
    }

    public function setHasData($status){
        $this->has_data = $status;
        return $this;
    }

    public function getData() {
        return $this->data;
    }

    public function setData(array $data) {
        $this->data = $data;
        if(!empty( $data )){
            $this->has_data = DataStatus::DATA_YES;
        }else{
            $this->has_data = DataStatus::DATA_NO;
        }
        return $this;
    }

    public function addData($key, $value){
        Arr::set( $this->data, $key, $value);
        return $this;
    }
    
}
