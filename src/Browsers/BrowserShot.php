<?php


namespace Nggiahao\Crawler\Browsers;


class BrowserShot implements BrowserInterface {
    
    protected $proxy;
    protected $timeout;
    
    public function getHtml( $url, $headers = [] ) {
        $chrome = \Spatie\Browsershot\Browsershot::url( $url )
                           ->userAgent( 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1' )
                           ->waitUntilNetworkIdle();
        
        if($this->timeout){
            $chrome->timeout( $this->timeout );
        }
        
        if($this->proxy){
            $chrome->setProxyServer( $this->proxy );
        }
        
        return $chrome->bodyHtml();
    }
    
    /**
     * @param array|string $proxy
     *
     * @return BrowserShot
     */
    public function setProxy( $proxy ) {
        if (is_array($proxy)) {
            $this->proxy = $proxy[0];
        } else {
            $this->proxy = $proxy;
        }
        
        return $this;
    }
    
    /**
     * @param int $timeout
     *
     * @return BrowserShot
     */
    public function setTimeout( $timeout ) {
        $this->timeout = $timeout;
        
        return $this;
    }
}
