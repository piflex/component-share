<?php

namespace BWC\Share\Sys;


final class DateTime
{
    const FORMAT_YMDHIS = 'Y-m-d H:i:s';


    /** @var int */
    private static $_now = null;

    /**
     * @return int
     */
    static function now() {
        if (self::$_now) {
            return self::$_now;
        } else {
            return time();
        }
    }

    /**
     * @param int|null $value
     */
    static function _setNow($value) {
        self::$_now = $value;
    }


    /**
     * @param string $format
     * @param int|null $ts
     * @return string
     */
    static function format($format = self::FORMAT_YMDHIS, $ts = null) {
        if (!$ts) {
            $ts = self::now();
        }
        if (!$format) {
            $format = 'Y-m-d H:i:s';
        }
        return date($format, $ts);
    }



    private function __construct() { }
}