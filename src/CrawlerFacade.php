<?php

namespace Nggiahao\Crawler;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Nggiahao\Crawler\Skeleton\SkeletonClass
 */
class CrawlerFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'crawler';
    }
}
