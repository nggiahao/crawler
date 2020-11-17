<?php
namespace Nggiahao\Crawler\Browsers;


use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class Guzzle implements BrowserInterface {
    
    /**
     * @var Client
     */
    protected $client;
    
    /**
     * @var CookieJar
     */
    protected $cookies;
    
    /**
     * @var array
     */
    protected $options = [];

    /**
     * Guzzle constructor.
     *
     * @param Client|null $client
     * @param bool $cookies
     */
    public function __construct( ?Client $client = null , bool $cookies = true) {
        if($client){
            $this->client = $client;
        }else{
            if($cookies){
                $jar = new CookieJar();
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
    
    /**
     * @param string $url
     * @param array $headers
     *
     * @return mixed|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getHtml( string $url, array $headers = [] ) {
        $response = $this->client->get( $url, [
            'headers' => $headers
        ] + $this->options );
        
        return $response->getBody()->getContents();
    }
    
    /**
     * @param array|string $proxy
     *
     * @return Guzzle
     */
    public function setProxy( $proxy ) {
        $this->options['proxy'] = $proxy;
        return $this;
    }
    
    /**
     * @param int $timeout
     *
     * @return Guzzle
     */
    public function setTimeout( $timeout ) {
        $this->options['timeout'] = $timeout;
        return $this;
    }
}
