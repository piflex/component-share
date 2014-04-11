<?php

namespace BWC\Share\Symfony\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class AjaxResponse
{
    /** @var bool */
    public $success = true;

    /** @var string[] */
    public $errors = [];

    /** @var mixed */
    public $data;


    /**
     * @param \Exception $e
     * @return AjaxResponse
     */
    public static function fromException(\Exception $e)
    {
        $response = new AjaxResponse();
        $response->success = false;
        $response->errors[] = $e->getMessage();

        return $response;
    }


    /**
     * @param $data
     * @return AjaxResponse
     */
    public static function fromJsonEncodable($data)
    {
        $response = new AjaxResponse();
        $response->data = $data;

        return $response;
    }


    /**
     * @param int $status
     * @return JsonResponse
     */
    public function toJsonResponse($status = 200)
    {
        return new JsonResponse($this, $status);
    }
} 