<?php

namespace BWC\Share\Config\Service;

use BWC\Share\Config\Model\ConfigInterface;
use BWC\Share\Config\Service\Model\ConfigManagerInterface;

class ConfigService implements ConfigServiceInterface
{
    /** @var  ConfigManagerInterface */
    protected $configManager;


    /** @var array   name => mixed */
    private $valueCache = array();


    /**
     * @param ConfigManagerInterface $configManager
     */
    public function __construct(ConfigManagerInterface $configManager)
    {
        $this->configManager = $configManager;
    }



    /**
     * @param string $name
     * @return bool|int|float|string|array|object
     */
    public function get($name)
    {
        if (false == array_key_exists($name, $this->valueCache)) {
            $dbConfig = $this->configManager->getByName($name);
            $this->valueCache[$name] = $this->decodeValue($dbConfig);
        }

        return $this->valueCache[$name];
    }

    /**
     * @param string $name
     * @param bool|int|float|string|array|object $value
     * @param string $type
     * @return void
     */
    public function set($name, $value, $type = ConfigInterface::TYPE_STRING)
    {
        $config = $this->configManager->getByName($name);

        if (null == $config) {
            $config = $this->configManager->create();
            $config->setName($name)
                ->setType($type);
        }

        $this->encodeValue($value, $config);

        $this->configManager->update($config);

        $this->valueCache[$name] = $value;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function delete($name)
    {
        $config = $this->configManager->getByName($name);

        if (null == $config) {

            return null;
        }

        $this->configManager->delete($config);

        return true;
    }


    /**
     * Returns decoded value from the config object
     * @param ConfigInterface $config
     * @return array|bool|float|int|object|string
     */
    protected function decodeValue(ConfigInterface $config = null)
    {
        if (false == $config) {
            return null;
        }

        switch ($config->getType()) {
            case ConfigInterface::TYPE_BOOL:
                $lc = strtolower($config->getValue());
                return $lc == '1' || $lc == 'true' || $lc == 'yes';

            case ConfigInterface::TYPE_INT:
                return intval($config->getValue());

            case ConfigInterface::TYPE_FLOAT:
                return floatval($config->getValue());

            case ConfigInterface::TYPE_JSON_ARRAY:
                return json_decode($config->getValue(), true);

            case ConfigInterface::TYPE_JSON_OBJECT:
                return json_decode($config->getValue(), false);

            case ConfigInterface::TYPE_OBJECT:
                return unserialize($config->getValue());
        }

        return $config->getValue();
    }

    /**
     * Encodes value and sets it to the config object
     * @param mixed $value
     * @param ConfigInterface $config
     */
    protected function encodeValue($value, ConfigInterface $config)
    {
        $config->setValue($value);

        switch ($config->getType()) {
            case ConfigInterface::TYPE_BOOL:
                $config->setValue((bool)$value);
                break;

            case ConfigInterface::TYPE_INT:
            case ConfigInterface::TYPE_FLOAT:
                $config->setValue((string)$value);
                break;

            case ConfigInterface::TYPE_JSON_ARRAY:
            case ConfigInterface::TYPE_JSON_OBJECT:
                $config->setValue(json_encode($value));
                break;

            case ConfigInterface::TYPE_OBJECT:
                $config->setValue(serialize($value));
                break;
        }
    }
} 