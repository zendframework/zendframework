<?php

namespace Zend\Cache\Storage;

class PostEvent extends Event
{

    /**
     * The result/return value
     *
     * @var mixed
     */
    protected $result;

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

