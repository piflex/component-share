<?php

namespace BWC\Share\Net\HttpClient;


interface HttpClientInterface
{
    /**
     * @return int
     */
    public function getTimeoutMS();

    /**
     * @param int $milliseconds
     * @return void
     */
    public function setTimeoutMS($milliseconds);

    /**
     * @param $path
     * @return void
     */
    public function setCaPath($path);

    /**
     * @param $file
     * @return void
     */
    public function setCaFile($file);

    /**
     * @return int
     */
    public function getStatusCode();

    /**
     * @param bool $value
     * @return void
     */
    public function looseSslCheck($value);

    /**
     * @return string
     */
    public function getErrorText();

        /**
     * @return string
     */
    public function getHeader();


    /**
     * @param string $username
     * @param string $password
     * @return void
     */
    public function setCredentials($username, $password);


    /**
     * @param string $type   mime type for Accept http header, like text/xml or application/json
     * @return void
     */
    public function accept($type);

    /**
     * @param string $url
     * @param array $queryData
     * @param array $arrHeaders
     * @return string
     */
    public function get($url, array $queryData = array(), array $arrHeaders = null);


    /**
     * @param string $url
     * @param array $queryData
     * @param string|array|object $postData
     * @param string|null $contentType
     * @param array $arrHeaders
     * @return string
     */
    public function post($url, array $queryData = array(), $postData, $contentType = null, array $arrHeaders = null);

    /**
     * @param string $url
     * @param array $queryData
     * @param array $arrHeaders
     * @return string
     */
    public function delete($url, array $queryData = array(), array $arrHeaders = null);


    /**
     * @param $url
     * @param $method
     * @param array $queryData
     * @param array|string $postData
     * @param null $contentType
     * @param array $arrHeaders
     * @return string
     */
    public function request($url, $method, array $queryData, $postData, $contentType = null, array $arrHeaders = null);

}