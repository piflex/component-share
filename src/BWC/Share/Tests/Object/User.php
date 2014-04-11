<?php

namespace BWC\Share\Tests\Object;

use BWC\Share\Object\DeserializeTrait;

class User
{
    use DeserializeTrait;

    public $id;
    public $name;
    public $email;
}