<?php

namespace BWC\Share\Security\TokenGenerator;

interface TokenGeneratorInterface
{
    /**
     * @return string
     */
    public function generateToken();
} 