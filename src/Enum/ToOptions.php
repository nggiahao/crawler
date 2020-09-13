<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2019-09-23
 * Time: 13:06
 */

namespace Nggiahao\Crawler\Enum;


trait ToOptions {

    /**
     * @return array
     * @throws \ReflectionException
     */
    public static function toOptions()
    {
        $class = \get_called_class();

        if (!isset(static::$cache[$class])) {
            $reflection            = new \ReflectionClass($class);
            static::$cache[$class] = $reflection->getConstants();
        }

        $array = static::$cache[$class];
        return array_flip( $array );
    }
}
