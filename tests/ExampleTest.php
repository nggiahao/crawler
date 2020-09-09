<?php

namespace Nggiahao\Crawler\Tests;

use Orchestra\Testbench\TestCase;
use Nggiahao\Crawler\CrawlerServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [CrawlerServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
