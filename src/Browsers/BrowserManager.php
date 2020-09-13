<?php

namespace Nggiahao\Crawler\Browsers;

class BrowserManager {
    
    protected static $proxy = [];
    protected static $timeout;
    protected static $drivers = [];
    
    
    /**
     * @param $driver
     *
     * @return BrowserInterface
     * @throws \Exception
     */
    public static function get($driver = "guzzle"){
        if(!isset( self::$drivers[$driver])){
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
     * @param $driver
     *
     * @return BrowserInterface
     * @throws \Exception
     */
    protected static function makeBrowser($driver = "guzzle", $session = ''){
        switch ($driver){
            case "phantomjs":
                return new PhantomJsLocal();
                break;
            case "chrome":
                return new BrowserShot();
                break;
            case "guzzle":
                return new Guzzle();
                break;
            default:
                throw new \Exception("No browser match with driver " . $driver);
        }
    }
    
    public static function setProxies(array $proxies){
        self::$proxy = $proxies;
    }
    
    public static function setTimeout($timeout){
        self::$timeout = $timeout;
    }
    
}
