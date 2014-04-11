<?php

namespace BWC\Share\Object;

final class ObjectHelper
{
    private function __construct() { }


    public static function copyExistingProperties($from, &$to, $stripPrefix = null) {
        $fromVars = null;
        $toVars = null;
        if (is_object($from)) {
            $fromVars = get_object_vars($from);
        } else if (is_array($from)) {
            $fromVars = $from;
        }
        if ($fromVars === null) throw new \InvalidArgumentException('Source must be an object or array');
        if (!is_object($to)) throw new \InvalidArgumentException('Destination must be an object');
        $toVars = get_object_vars($to);
        foreach ($fromVars as $prop=>$value) {
            if (array_key_exists($prop, $toVars)) {
                $to->$prop = $value;
            }
            if ($stripPrefix && strpos($prop, $stripPrefix) === 0) {
                $p = substr($prop, strlen($stripPrefix));
                if (array_key_exists($p, $toVars)) {
                    $to->$p = $value;
                    unset($toVars[$p]);
                }
            }
        }
    }

}