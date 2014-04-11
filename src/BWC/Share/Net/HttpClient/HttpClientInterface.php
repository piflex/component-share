<?php

namespace BWC\Share\Net\HttpClient;


interface HttpClientInterface
{
    /**
     * @return int
     */
    function getTimeoutMS();

    /**
     * @param int $milliseconds
     * @return void
     */
    function setTimeoutMS($milliseconds);

    /**
     * @return int
     */
    function getStatusCode();

    /**
     * @return string
     */
    function getHeader();


    /**
     * @param string $username
     * @param string $password
     * @return void
     */
    function setCredentials($username, $password);


    /**
     * @param string $type   mime type for Accept http header, like text/xml or application/json
     * @return void
     */
    function accept($type);

    /**
     * @param string $url
     * @param array $queryData
     * @param array $arrHeaders
     * @return string
     */
    function get($url, array $queryData = array(), array $arrHeaders = null);


    /**
     * @param string $url
     * @param array $queryData
     * @param string|array|object $postData
     * @param string|null $contentType
     * @param array $arrHeaders
     * @return string
     */
    function post($url, array $queryData = array(), $postData, $contentType = null, array $arrHeaders = null);

    /**
     * @param string $url
     * @param array $queryData
     * @param array $arrHeaders
     * @return string
     */
    function delete($url, array $queryData = array(), array $arrHeaders = null);


    /**
     * @param $url
     * @param $method
     * @param array $queryData
     * @param array|string $postData
     * @param null $contentType
     * @param array $arrHeaders
     * @return string
     */
    function request($url, $method, array $queryData, $postData, $contentType = null, array $arrHeaders = null);

}