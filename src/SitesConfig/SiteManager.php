<?php


namespace Nggiahao\Crawler\SitesConfig;


use Illuminate\Support\Arr;

class SiteManager {

    protected $sites;
    /**
     * SiteManager constructor.
     *
     */
    public function __construct( ) {
        $this->load(config('crawler.site_config'));
    }

    /**
     * @param $sites
     */
    protected function load($sites) {
        $sites = array_unique(Arr::wrap($sites));

        foreach ($sites as $site) {
            $this->sites[] = [
                'class' => $site,
                'name' => strtolower( preg_replace( "/^.*\\\([^\\\]+)$/ui", "$1", $site ) ),
            ];
        }
    }

    /**
     * @param $sites
     *
     * @return array
     */
    public function getSiteNames($sites){
        $sites = array_unique(Arr::wrap($sites));

        $names = [];

        foreach ($this->sites as $site_info){
            foreach ($sites as $name){
                $name_lowercase = strtolower( $name );
                if($site_info['class'] == $name || $site_info['name'] == $name_lowercase || $site_info['name'] == $name){
                    $names[] = $site_info['name'];
                }
            }
        }

        return $names;
    }

    /**
     * @param string $name name or class name
     *
     * @return SiteInterface
     * @throws SiteNotFoundException
     */
    public function getSiteConfig($name){
        $name_lowercase = strtolower( $name );
        foreach ($this->sites as $site_info){
            if($site_info['class'] == $name || $site_info['name'] == $name_lowercase || $site_info['name'] == $name){
                return new $site_info['class'];
            }
        }
        throw new SiteNotFoundException("Can not find site match with name : " . $name);
    }

}
