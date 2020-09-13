<?php
if (!function_exists('hash_url')) {
    /**
     * @param string $algo <p>
     * Name of selected hashing algorithm (i.e. "md5", "sha256", "haval160,4", etc..)
     * </p>
     * @param string $url <p>
     * Message to be hashed.
     * </p>
     *
     * @return string
     */
    function hash_url($url, $algo = 'sha256') {
        $uri = preg_replace( "/^(https?)?:\/\//", "", $url);
        return hash( $algo ,$uri);
    }
}
