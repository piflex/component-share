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
     * @param \DateTime|null $expiresAt
     * @return void
     */
    public function set($name, $value, $type = ConfigInterface::TYPE_STRING, \DateTime $expiresAt = null);

    /**
     * @param string $name
     * @return bool
     */
    public function delete($name);

    /**
     * @param int|null $limit
     * @return int
     */
    public function deleteExpired($limit = null);

} 