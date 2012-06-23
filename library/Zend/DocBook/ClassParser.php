<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_DocBook
 */

namespace Zend\DocBook;

use ReflectionMethod;
use Zend\Filter\Word\CamelCaseToDash as CamelCaseToDashFilter;
use Zend\Code\Reflection\ClassReflection;

/**
 * @category   Zend
 * @package    Zend_DocBook
 */
class ClassParser
{
    /**
     * @var ClassReflection
     */
    protected $reflection;

    /**
     * @var string Normalized DocBook ID
     */
    protected $id;

    /**
     * @var ClassMethod[] Array of ClassMethod objects representing public methods
     */
    protected $methods;

    /**
     * Constructor
     *
     * @param ClassReflection $class
     * @return self
     */
    public function __construct(ClassReflection $class)
    {
        $this->reflection = $class;
    }

    /**
     * Retrieve DocBook ID for this class
     *
     * @return string
     */
    public function getId()
    {
        if (null !== $this->id) {
            return $this->id;
        }

        $class  = $this->reflection->getName();
        $id     = '';
        $filter = new CamelCaseToDashFilter();

        foreach (explode('\\', $class) as $segment) {
            $id .= $filter->filter($segment) . '.';
        }

        $id = strtolower(rtrim($id, '.'));

        $this->id = $id;
        return $this->id;
    }

    /**
     * Get class name
     *
     * @return string
     */
    public function getName()
    {
        return $this->reflection->getName();
    }

    /**
     * Retrieve parsed methods for this class
     *
     * @return ClassMethod[] Array of ClassMethod objects
     */
    public function getMethods()
    {
        if (null !== $this->methods) {
            return $this->methods;
        }

        $rMethods = $this->reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        $methods  = array();
        foreach ($rMethods as $method) {
            $methods[] = new ClassMethod($method);
        }

        $this->methods = $methods;
        return $this->methods;
    }
}
