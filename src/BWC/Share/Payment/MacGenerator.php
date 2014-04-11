<?php

namespace BWC\Share\Payment;

class MacGenerator
{
    /**
     * @param string $string
     * @param string $key
     * @return string
     */
    public function generateFromString($string, $key)
    {
        $key = pack('H*', $key);
        $mac = hash_hmac('sha256', $string, $key);

        return $mac;
    }
} 