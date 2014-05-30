<?php

namespace BWC\Share\Security\FingerprintCalculator;

class FingerprintCalculator implements FingerprintCalculatorInterface
{
    /**
     * @param string $pem
     * @return string
     */
    public function get($pem)
    {
        $output = $pem;
        $output = str_replace('-----BEGIN CERTIFICATE-----', '', $output);
        $output = str_replace('-----END CERTIFICATE-----', '', $output);

        $output = base64_decode($output);

        $fingerprint = sha1($output);

        return $fingerprint;
    }
} 