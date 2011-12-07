<?php

namespace Zend\Cache\Storage;
use Exception,
    ArrayObject;

class ExceptionEvent extends Event
{

    /**
     * The exception  to be throw
     *
     * @var Exception
     */
    protected $exception;

    /**
     * Throw the exception or use the result
     *
     * @var boolean
     */
    protected $throwException = true;

    /**
     * The result/return value
     * if the exception shouldn't throw
     *
     * @var mixed
     */
    protected $result = false;

    /**
     * Constructor
     *
     * Accept a target and its parameters.
     *
     * @param  string $name Event name
     * @param  Zend\Cache\Storage\Adapter $storage
     * @param  ArrayObject $params
     * @param  Exception $exception
     * @return void
     */
    public function __construct($name, Adapter $storage, ArrayObject $params, Exception $exception)
    {
        parent::__construct($name, $storage, $params);
        $this->setException($exception);
    }

    /**
     * Set the exception to be throw
     *
     * @param Exception $exception
     */
    public function setException(Exception $exception)
    {
        $this->exception = $exception;
        return $this;
    }

    /**
     * Get the exception to be throw
     *
     * @return Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Throw the exception or use the result
     *
     * @param boolean $flag
     */
    public function setThrowException($flag)
    {
        $this->throwException = (bool)$flag;
        return $this;
    }

    /**
     * Throw the exception or use the result
     *
     * @return boolean
     */
    public function getThrowException()
    {
        return $this->throwException;
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

