<?php

namespace BWC\Share\Config\Model;

class Config implements ConfigInterface
{
    /** @var  string */
    protected $name;

    /** @var  string */
    protected $type;

    /** @var  bool|int|string|array|object */
    protected $value;




    /**
     * @param string $name
     * @return $this|ConfigInterface
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $type
     * @return $this|ConfigInterface
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param array|bool|int|object|string $value
     * @return $this|ConfigInterface
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return array|bool|int|object|string
     */
    public function getValue()
    {
        return $this->value;
    }

} 