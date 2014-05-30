<?php

namespace BWC\Share\Security\FingerprintCalculator;

interface FingerprintCalculatorInterface
{
    /**
     * @param string $pem
     * @return string
     */
    public function get($pem);

} 