<?php

namespace BWC\Share\Config\Service;

use BWC\Share\Config\Model\ConfigInterface;

interface ConfigServiceInterface
{
    /**
     * @param string $name
     * @return bool|int|float|string|array|object
     */
    public function get($name);

    /**
     * @param string $name
     * @param bool|int|float|string|array|object $value
     * @param string $type
     * @return void
     */
    public function set($name, $value, $type = ConfigInterface::TYPE_STRING);

    /**
     * @param string $name
     * @return bool
     */
    public function delete($name);

} 