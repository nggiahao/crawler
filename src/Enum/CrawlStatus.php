<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2020-06-10
 * Time: 16:01
 */

namespace Nggiahao\Crawler\Enum;


use MyCLabs\Enum\Enum;

class CrawlStatus extends Enum {

    use ToOptions;

    public const CRAWL_INIT = 0;
    public const CRAWL_VISITING = 10;
    public const CRAWL_DONE = 200; // default success code
    public const CRAWL_FAIL = 1000; // default error code, or response code for specific error
    
}
