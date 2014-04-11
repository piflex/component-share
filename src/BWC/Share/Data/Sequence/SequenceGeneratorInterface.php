<?php

namespace BWC\Share\Data\Sequence;


interface SequenceGeneratorInterface
{
    /**
     * @return string
     */
    function generate();
}