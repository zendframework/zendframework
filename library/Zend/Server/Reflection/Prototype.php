<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Server
 */

namespace Zend\Server\Reflection;

/**
 * Method/Function prototypes
 *
 * Contains accessors for the return value and all method arguments.
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage Zend_Server_Reflection
 */
class Prototype
{
    /** @var ReflectionParameter[] */
    protected $params;

    /**
     * Constructor
     *
     * @param ReflectionReturnValue $return
     * @param ReflectionParameter[] $params
     * @throws Exception\InvalidArgumentException
     */
    public function __construct(ReflectionReturnValue $return, array $params = array())
    {
        $this->return = $return;

        foreach ($params as $param) {
            if (!$param instanceof ReflectionParameter) {
                throw new Exception\InvalidArgumentException('One or more params are invalid');
            }
        }

        $this->params = $params;
    }

    /**
     * Retrieve return type
     *
     * @return string
     */
    public function getReturnType()
    {
        return $this->return->getType();
    }

    /**
     * Retrieve the return value object
     *
     * @return \Zend\Server\Reflection\ReflectionReturnValue
     */
    public function getReturnValue()
    {
        return $this->return;
    }

    /**
     * Retrieve method parameters
     *
     * @return ReflectionParameter[] Array of {@link \Zend\Server\Reflection\ReflectionParameter}s
     */
    public function getParameters()
    {
        return $this->params;
    }
}
