<?php

namespace BWC\Share\Config\Service;

use BWC\Share\Config\Model\ConfigInterface;
use BWC\Share\Config\Service\Model\ConfigManagerInterface;
use BWC\Share\Sys\DateTime;

class ConfigService implements ConfigServiceInterface
{
    /** @var  ConfigManagerInterface */
    protected $configManager;


    /** @var array   name => [mixed, expiresAt] */
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
            $this->valueCache[$name] = array(
                $this->decodeValue($dbConfig),
                $dbConfig && $dbConfig->getExpiresAt() ? $dbConfig->getExpiresAt()->getTimestamp() : null
            );
        }

        $data = @$this->valueCache[$name];

        if ($data) {
            if ($data[1] > DateTime::now()) {
                return $data[0];
            } else if (isset($dbConfig)) {
                $this->configManager->delete($dbConfig);
            }
        }

        return null;
    }

    /**
     * @param string $name
     * @param bool|int|float|string|array|object $value
     * @param string $type
     * @param \DateTime|null $expiresAt
     * @return void
     */
    public function set($name, $value, $type = ConfigInterface::TYPE_STRING, \DateTime $expiresAt = null)
    {
        $config = $this->configManager->getByName($name);

        if (null == $config) {
            $config = $this->configManager->create()
                ->setName($name)
                ->setType($type)
                ->setExpiresAt($expiresAt)
            ;
        }

        $this->encodeValue($value, $config);

        $this->configManager->update($config);

        $this->valueCache[$name] = array(
            $value,
            $expiresAt ? $expiresAt->getTimestamp() : null
        );
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
     * @param int|null $limit
     * @return int
     */
    public function deleteExpired($limit = null)
    {
        return $this->configManager->deleteExpired(DateTime::now(), $limit);
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