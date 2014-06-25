<?php

namespace BWC\Share\Config\Service\Model;

use BWC\Share\Config\Model\ConfigInterface;

interface ConfigManagerInterface
{
    /**
     * @return string
     */
    public function getClass();

    /**
     * @return ConfigInterface
     */
    public function create();

    /**
     * @param ConfigInterface $config
     * @return void
     */
    public function delete(ConfigInterface $config);

    /**
     * @param string $name
     * @return ConfigInterface|null
     */
    public function getByName($name);

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return ConfigInterface[]
     */
    public function find(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param ConfigInterface $object
     * @return void
     */
    public function update(ConfigInterface $object);

}