<?php

namespace BWC\Share\Config\Model;

interface ConfigInterface
{
    const TYPE_BOOL = 'bool';
    const TYPE_INT = 'int';
    const TYPE_FLOAT = 'float';
    const TYPE_STRING = 'string';
    const TYPE_JSON_ARRAY = 'json_array';
    const TYPE_JSON_OBJECT = 'json_object';
    const TYPE_OBJECT = 'object';

    /**
     * @param string $name
     * @return $this|ConfigInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $type
     * @return $this|ConfigInterface
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param array|bool|int|object|string $value
     * @return $this|ConfigInterface
     */
    public function setValue($value);

    /**
     * @return array|bool|int|object|string
     */
    public function getValue();

} 