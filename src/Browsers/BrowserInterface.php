<?php

namespace Nggiahao\Crawler\Browsers;

interface BrowserInterface {
    
    public function setProxy($proxies);
    
    public function setTimeout($timeout);
    
    public function getHtml($url, $headers = []);
    
}
