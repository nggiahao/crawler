<?php

namespace Nggiahao\Crawler\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Nggiahao\Crawler\CrawlerServiceProvider;

class TestCase extends BaseTestCase
{

    protected function getPackageProviders($app)
    {
        return [CrawlerServiceProvider::class];
    }
}
