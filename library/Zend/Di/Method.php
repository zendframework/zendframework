<?php
namespace Zend\Di;

/**
 * A reference to an injectible method in a class
 *
 * Stores both the method name and arguments to pass.
 * 
 * @copyright Copyright (C) 2006-Present, Zend Technologies, Inc.
 * @license   New BSD {@link http://framework.zend.com/license/new-bsd}
 */
class Method implements InjectibleMethod
{
    /**
     * Method name
     * @var string
     */
    protected $name;

    /**
     * Arguments to pass to the method
     * @var array
     */
    protected $params;

    /**
     * Construct the method signature
     * 
     * @param  strinb $name 
     * @param  array $params 
     * @return void
     */
    public function __construct($name, array $params)
    {
        $this->name = $name;
        $this->params = $params;
    }

    /**
     * Retrieve the method name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Retrieve the arguments to pass to the method
     * 
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}
