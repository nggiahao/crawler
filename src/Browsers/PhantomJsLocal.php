<?php
namespace Nggiahao\Crawler\Browsers;

use Nggiahao\Crawler\Browsers\Phantomjs\RenderWithJs;

class PhantomJsLocal implements BrowserInterface {
    
    public function getHtml( $url, $headers = [] ) {
        $response = RenderWithJs::render( $url );
        return $response['html'];
    }
    
    public function setProxy( $proxies ) {
        // TODO: Implement setProxy() method.
    }
    
    public function setTimeout( $proxies ) {
        // TODO: Implement setTimeout() method.
    }
}
