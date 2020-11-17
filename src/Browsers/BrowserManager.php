<?php

namespace Nggiahao\Crawler\Browsers;

use Nggiahao\Crawler\Exception\NotFoundBrowserDriver;

class BrowserManager {
    
    /** @var array|string */
    protected static $proxy;
    
    /** @var int $timeout */
    protected static $timeout;
    
    /** @var array $drivers */
    protected static $drivers = [];
    
    
    /**
     * @param string $driver
     *
     * @return BrowserInterface
     * @throws NotFoundBrowserDriver
     */
    public static function get(string $driver = "guzzle"){
        if(empty(self::$drivers[$driver])){
            self::$drivers[$driver] = self::makeBrowser( $driver );
            if(!empty( self::$proxy)){
                self::$drivers[$driver]->setProxy(self::$proxy);
            }
            if(!empty( self::$timeout)){
                self::$drivers[$driver]->setTimeout(self::$timeout);
            }
        }
        return self::$drivers[$driver];
    }
    
    /**
     * @param string $driver
     *
     * @return BrowserInterface
     * @throws NotFoundBrowserDriver
     */
    protected static function makeBrowser(string $driver = "chrome") {
        switch ($driver){
            case "chrome":
                return new BrowserShot();
            case "guzzle":
                return new Guzzle();
            default:
                throw new NotFoundBrowserDriver("No browser match with driver " . $driver);
        }
    }
    
    public static function setProxy(array $proxy){
        self::$proxy = $proxy;
    }
    
    public static function setTimeout($timeout){
        self::$timeout = $timeout;
    }
    
}
