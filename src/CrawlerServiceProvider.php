<?php

namespace Nggiahao\Crawler;

use Illuminate\Support\ServiceProvider;

class CrawlerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/crawler.php' => config_path('crawler.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../database/migrations/create_crawl_urls_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_crawl_urls_table.php'),
                // you can add any number of migrations here
            ], 'migrations');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        require_once __DIR__ . '/Helpers/helpers.php';

        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/crawler.php', 'crawler');
    }
}
