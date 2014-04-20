<?php

namespace BWC\Share\Github;

use BWC\Share\Object\DeserializeTrait;

class GithubAccessResponse
{
    use DeserializeTrait;

    /** @var  string */
    public $access_token;

    /** @var string[] */
    public $scope;

    /** @var  string */
    public $token_type;

    /** @var  string */
    public $error;

    /** @var  string */
    public $error_description;

    /** @var  string */
    public $error_uri;



    public function deserializeExtra($arr)
    {
        $this->scope = explode(',', $this->scope);
    }
} 