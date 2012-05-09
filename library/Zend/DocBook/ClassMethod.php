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

use Zend\Code\NameInformation;
use Zend\Code\Reflection\MethodReflection;
use Zend\Code\Reflection\DocBlockReflection;
use Zend\Filter\Word\CamelCaseToDash as CamelCaseToDashFilter;


/**
 * @category   Zend
 * @package    Zend_DocBook
 */
class ClassMethod
{
    /**
     * @var MethodReflection
     */
    protected $reflection;

    /**
     * PHP internal types
     *
     * @var array
     */
    protected $internalTypes = array(
        'boolean',
        'bool',
        'true',
        'false',
        'integer',
        'int',
        'double',
        'float',
        'string',
        'array',
        'object',
        'resource',
        'null',
    );

    /**
     * @var string[] Imports in play for this method
     */
    protected $uses;

    /**
     * @var string Normalized DocBook identifier
     */
    protected $id;

    /**
     * @var string Name of declaring class, minus namespace
     */
    protected $class;

    /**
     * @var string Namespace of declaring class
     */
    protected $namespace;

    /**
     * @var \Zend\Code\Reflection\DocBlock\ParamTag[] Array of DocBlock tags
     */
    protected $parameterAnnotations;

    /**
     * Constructor
     *
     * @param  MethodReflection $reflection
     * @return self
     */
    public function __construct(MethodReflection $reflection)
    {
        $this->reflection = $reflection;
    }

    /**
     * Retrieve method name
     *
     * @return string
     */
    public function getName()
    {
        return $this->reflection->getName();
    }

    /**
     * Get normalized method identifier
     *
     * @return string
     */
    public function getId()
    {
        if (null !== $this->id) {
            return $this->id;
        }

        $namespace = $this->getNamespace();
        $class     = $this->getClass();
        $name      = $this->getName();
        $id        = '';
        $filter    = new CamelCaseToDashFilter();

        foreach (explode('\\', $namespace) as $segment) {
            $id .= $filter->filter($segment) . '.';
        }
        $id .= $filter->filter($class)
               . '.methods.'
               . $filter->filter($name);

        $this->id = strtolower($id);
        return $this->id;
    }

    /**
     * Retrieve method short description
     *
     * @return string
     */
    public function getShortDescription()
    {
        $rDocBlock = $this->reflection->getDocBlock();
        if ($rDocBlock instanceof DocBlockReflection) {
            return $rDocBlock->getShortDescription();
        }
        return '';
    }

    /**
     * Retrieve method long description
     *
     * @return string
     */
    public function getLongDescription()
    {
        $rDocBlock = $this->reflection->getDocBlock();
        if ($rDocBlock instanceof DocBlockReflection) {
            return $rDocBlock->getLongDescription();
        }
        return '';
    }

    /**
     * Retrieve method return type
     *
     * @return string
     */
    public function getReturnType()
    {
        $rDocBlock = $this->reflection->getDocBlock();
        if (!$rDocBlock instanceof DocBlockReflection) {
            return 'void';
        }

        $return = $rDocBlock->getTag('return');

        if (!$return) {
            return 'void';
        }

        return $this->resolveTypes($return->getType());
    }

    /**
     * Return method argument prototype
     *
     * @return string
     */
    public function getPrototype()
    {
        $params = array();

        $reflectionParams = $this->getParameterTags();

        foreach ($this->reflection->getParameters() as $index => $param) {
            $types = '';
            if (isset($reflectionParams[$index])) {
                $type  = $reflectionParams[$index]->getType();
                $types = $this->resolveTypes($type);
            }

            $default = '';
            if ($param->isOptional()) {
                $defaultValue = var_export($param->getDefaultValue(), 1);
                $defaultValue = $this->resolveTypes($defaultValue);

                // Skip null values, but report all others
                if ('null' != strtolower($defaultValue)) {
                    $default = ' = ' . $this->resolveTypes($defaultValue);
                }
            }

            $prototype = sprintf('%s $%s%s', $types, $param->getName(), $default);
            $params[]  = $prototype;
        }

        return implode(', ', $params);
    }

    /**
     * Resolve the types provided via an @param or @return annotation
     *
     * @param  string $value
     * @return string
     */
    protected function resolveTypes($value)
    {
        $values = explode('|', trim($value));
        array_walk($values, 'trim');

        $nameInformation = new NameInformation(
            $this->getNamespace(),
            $this->getUses()
        );

        foreach ($values as $index => $value) {
            // Is it an internal type?
            if (in_array(strtolower($value), $this->internalTypes)) {
                continue;
            }
            $values[$index] = $nameInformation->resolveName($value);
        }

        return implode('|', $values);
    }

    /**
     * Get the namespace of the class containing this method
     *
     * @return string
     */
    protected function getNamespace()
    {
        if (null !== $this->namespace) {
            return $this->namespace;
        }

        $r               = $this->reflection->getDeclaringClass();
        $this->namespace = $r->getNamespaceName();
        return $this->namespace;
    }

    /**
     * Get the class containing this method, without the leading namespace
     *
     * @return string
     */
    protected function getClass()
    {
        if (null !== $this->class) {
            return $this->class;
        }

        $r               = $this->reflection->getDeclaringClass();
        $this->class     = $r->getShortName();
        $this->namespace = $r->getNamespaceName();

        return $this->class;
    }

    /**
     * Get import statements and aliases from the class containing this method
     *
     * @return string[]
     */
    protected function getUses()
    {
        if (null !== $this->uses) {
            return $this->uses;
        }

        $rClass = $this->reflection->getDeclaringClass();
        $rFile  = $rClass->getDeclaringFile();

        $this->uses = $rFile->getUses();

        return $this->uses;
    }

    /**
     * Get parameter annotations from DocBlock
     *
     * @return \Zend\Code\Reflection\DocBlock\ParamTag[]
     */
    protected function getParameterTags()
    {
        if (null !== $this->parameterAnnotations) {
            return $this->parameterAnnotations;
        }

        $rDocBlock = $this->reflection->getDocBlock();
        if ($rDocBlock instanceof DocBlockReflection) {
            $params = $rDocBlock->getTags('param');
        } else {
            $params = array();
        }

        $this->parameterAnnotations = $params;
        return $this->parameterAnnotations;
    }
}
