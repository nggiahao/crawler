<?php

namespace Nggiahao\Crawler\Browsers;

interface BrowserInterface {
    
    /**
     * set proxy
     * @param array|string $proxy
     *
     * @return mixed
     */
    public function setProxy($proxy);
    
    /**
     * set timeout
     * @param int $timeout
     *
     * @return mixed
     */
    public function setTimeout(int $timeout);
    
    /**
     * get html
     * @param string $url
     * @param array $headers
     *
     * @return mixed
     */
    public function getHtml(string $url, array $headers = []);
    
}
