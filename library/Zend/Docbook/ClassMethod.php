<?php

namespace Zend\Docbook;

use Zend\Filter\Word\CamelCaseToDash as CamelCaseToDashFilter,
    Zend\Code\Reflection\MethodReflection,
    Zend\Code\Reflection\DocBlockReflection;

class ClassMethod
{
    /**
     * @var ReflectionMethod
     */
    protected $reflection;

    /**
     * PHP internal types
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
     * @var array Imports in play for this method
     */
    protected $uses;

    /**
     * @var string Normalized docbook identifier
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
     * @var array Array of Zend\Reflection\ReflectionDocblockTag
     */
    protected $parameterAnnotations;

    /**
     * Constructor
     *
     * @param  ReflectionMethod $reflection
     * @return void
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
        $rDocblock = $this->reflection->getDocblock();
        if ($rDocblock instanceof DocBlockReflection) {
            return $rDocblock->getShortDescription();
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
        $rDocblock = $this->reflection->getDocblock();
        if ($rDocblock instanceof DocBlockReflection) {
            return $rDocblock->getLongDescription();
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
        $rDocblock = $this->reflection->getDocblock();
        if (!$rDocblock instanceof DocBlockReflection) {
            return 'void';
        }

        $return = $rDocblock->getTag('return');

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

            $proto = sprintf('%s $%s%s', $types, $param->getName(), $default);
            $params[] = $proto;
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

        $nameInformation = new \Zend\Code\NameInformation(
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

        $r = $this->reflection->getDeclaringClass();
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

        $r = $this->reflection->getDeclaringClass();
        $this->class     = $r->getShortName();
        $this->namespace = $r->getNamespaceName();

        return $this->class;
    }

    /**
     * Get import statements and aliases from the class containing this method
     *
     * @return array
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
     * Get parameter annotations from docblock
     *
     * @return array
     */
    protected function getParameterTags()
    {
        if (null !== $this->parameterAnnotations) {
            return $this->parameterAnnotations;
        }

        $rDocblock = $this->reflection->getDocblock();
        if ($rDocblock instanceof DocBlockReflection) {
            $params    = $rDocblock->getTags('param');
        } else {
            $params = array();
        }

        $this->parameterAnnotations = $params;
        return $this->parameterAnnotations;
    }
}
