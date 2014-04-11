<?php

namespace BWC\Share\Data\Sequence;

use BWC\Share\Object\UUID;

class UUIDSequenceGenerator implements SequenceGeneratorInterface
{
    /**
     * @return string
     */
    function generate() {
        return UUID::generate();
    }

}