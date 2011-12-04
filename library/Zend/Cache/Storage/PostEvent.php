<?php

namespace Zend\Cache\Storage;
use ArrayObject;

class PostEvent extends Event
{

    /**
     * The result/return value
     *
     * @var mixed
     */
    protected $result;

    /**
     * Constructor
     *
     * Accept a target and its parameters.
     *
     * @param  string $name Event name
     * @param  Zend\Cache\Storage\Adapter $storage
     * @param  ArrayObject $params
     * @param  mixed $result
     * @return void
     */
    public function __construct($name, Adapter $storage, ArrayObject $params, &$result)
    {
        parent::__construct($name, $storage, $params);
        $this->setResult($result);
    }

    /**
     * Set the result/return value
     *
     * @param mixed $value
     * @return Zend\Cache\Storage\PostEvent
     */
    public function setResult(&$value)
    {
        $this->result = & $value;
        return $this;
    }

    /**
     * Get the result/return value
     *
     * @return mixed
     */
    public function & getResult()
    {
        return $this->result;
    }

}

