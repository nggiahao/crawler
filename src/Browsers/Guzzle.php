<?php
namespace Nggiahao\Crawler\Browsers;


use GuzzleHttp\Client;

class Guzzle implements BrowserInterface {
    
    protected $client;
    protected $cookies;
    protected $options = [];

    /**
     * Guzzle constructor.
     *
     * @param Client|null $client
     * @param bool $cookies
     */
    public function __construct( ?Client $client = null , $cookies = true) {
        if($client){
            $this->client = $client;
        }else{
            if($cookies){
                $jar = new \GuzzleHttp\Cookie\CookieJar();
                $this->cookies = $jar;
            }
            $this->client = new Client([
                'cookies' => $this->cookies,
                'headers' => [
                    'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.106 Safari/537.36',
                ],
            ]);
        }
    }
    
    
    public function getHtml( $url, $headers = [] ) {
        $response = $this->client->get( $url, [
            'headers' => $headers
        ] + $this->options );
        return $response->getBody()->getContents();
    }
    
    public function setProxy( $proxies ) {
        $this->options['proxy'] = $proxies[0];
    }
    
    public function setTimeout( $timeout ) {
        $this->options['timeout'] = $timeout;
    }
}
