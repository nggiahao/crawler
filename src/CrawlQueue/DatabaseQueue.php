<?php


namespace Nggiahao\Crawler\CrawlQueue;


use Illuminate\Support\Facades\DB;
use Nggiahao\Crawler\Enum\CrawlStatus;
use Nggiahao\Crawler\Enum\DataStatus;
use PDO;
use Nggiahao\Crawler\CrawlUrl;

class DatabaseQueue implements QueueInterface {
    
    protected $table = 'crawl_urls';
    
    public function reset( $site, $keep_data = false ) {
        return DB::table( $this->table )
            ->where( 'site', $site )
            ->when($keep_data, function($query){
                $query->where('has_data', '<>', DataStatus::DATA_YES);
            })
            ->delete();
    }
    
    public function resume( $site ) {
        return DB::table( $this->table )
                  ->where( 'site', $site )
                  ->where( 'status', CrawlStatus::CRAWL_VISITING )
                  ->update( [ 'status' => CrawlStatus::CRAWL_INIT ] );
    }
    
    public function findByUrl( $url, $site = null ) {
        $hash = hash_url( $url );
        
        return DB::table( $this->table )
                  ->when($site, function ($query) use ($site) {
                      $query->where( 'site', $site );
                  })
                  ->where( 'url_hash', $hash )->first();
    }
    
    public function push( CrawlUrl $crawlUrl ) {
        if ( self::exists( $crawlUrl->url ) ) {
            return false;
        }
        
        $inserted = DB::table( $this->table )
                       ->insertGetId( $crawlUrl->toArray() );
        
        if ( $inserted ) {
            $crawlUrl->setId( $inserted );
        }
        
        return $crawlUrl;
    }
    
    public function exists( $url, $site = null ): bool {

        return DB::table( $this->table )
                  ->when($site, function ($query) use ($site) {
                      $query->where( 'site', $site );
                  })
                  ->where( 'url_hash', hash_url( (string)$url) )
                  ->exists();
    }
    
    public function hasPendingUrls( $sites ): bool {

        $sites = is_array( $sites ) ? $sites : [ $sites ];
        
        return DB::table( $this->table )
                  ->whereIn( 'site', $sites )
                  ->where( 'status', CrawlStatus::CRAWL_INIT )
                  ->exists();
        
    }

    /**
     * Get pending url and mark as processing
     *
     * @param $sites
     *
     * @return mixed
     */
    public function firstPendingUrl( $sites ): ?CrawlUrl {
        $sites = is_array( $sites ) ? $sites : [ $sites ];

        $first = DB::table( $this->table )
                                  ->lock(self::getLockForPopping())
                                  ->whereIn( 'site', $sites )
                                  ->where('status', CrawlStatus::CRAWL_INIT)
                                  ->orderBy('visited')
                                  ->first();
        if($first){
            $crawlUrl = CrawlUrl::fromObject($first);
            self::changeProcessStatus( $crawlUrl, CrawlStatus::CRAWL_VISITING );
            return $crawlUrl;
        }else{
            return null;
        }
    }
    
    public function changeProcessStatus( CrawlUrl $crawlUrl, $status = null ) {
        $data = [ 'status' => $status ?? $crawlUrl->getStatus() ];
        if($data['status'] == CrawlStatus::CRAWL_VISITING){
            $data['visited'] = DB::raw( 'visited + 1' );
        }
        if($data['status'] == CrawlStatus::CRAWL_DONE){
            $data['has_data'] = $crawlUrl->hasData();
            $data['data'] = \GuzzleHttp\json_encode( $crawlUrl->getData());
        }
        return DB::table( $this->table )
                                ->where( 'id', $crawlUrl->getId() )
                                ->update( $data );
    }

    public function delay( CrawlUrl $crawlUrl ) {
        return DB::table( $this->table )
            ->where( 'id', $crawlUrl->getId() )
            ->update( [ 'status' => CrawlStatus::CRAWL_INIT, 'visited' => DB::raw( 'visited + 1' )]);
    }
    
    protected function getLockForPopping()
    {
        $databaseEngine = DB::connection()->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME);
        $databaseVersion = DB::connection()->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION);
        
        if ($databaseEngine == 'mysql' && ! strpos($databaseVersion, 'MariaDB') && version_compare($databaseVersion, '8.0.1', '>=') ||
            $databaseEngine == 'pgsql' && version_compare($databaseVersion, '9.5', '>=')) {
            return 'FOR UPDATE SKIP LOCKED';
        }
        
        return true;
    }
    
    public function updateData( CrawlUrl $crawlUrl ) {
        return DB::table( $this->table )
                  ->where( 'site', $crawlUrl->getSite() )
                  ->where( 'id', $crawlUrl->getId() )
                  ->update( [ 'data' => $crawlUrl->getData() ] );
    }

    public function insert(array $data) {
        return DB::table($this->table)
            ->insert($data);
    }
}
