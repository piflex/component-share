<?php

namespace BWC\Share\Object;


trait DeserializeTrait
{

    /**
     * @param $data
     * @return null|static
     */
    static function deserialize($data) {
        $result = null;
        $arr = null;
        if (is_string($data)) {
            $arr = json_decode($data, true);
        } else if (is_array($data)) {
            $arr = $data;
        } else if(is_object($data)){
            $arr = get_object_vars($data);
        }
        if ($arr) {
            /** @var $result DeserializeTrait */
            $result = new static();
            ObjectHelper::copyExistingProperties($arr, $result);
            $result->deserializeExtra($arr);
        }
        return $result;
    }

    function deserializeExtra($arr) { }

}