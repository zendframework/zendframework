<?php

namespace Zend\Docbook;

use Zend\Filter\Word\CamelCaseToDash as CamelCaseToDashFilter,
    Zend\Code\Reflection\MethodReflection as ReflectionMethod,
    Zend\Code\Scanner\Util as ScannerUtil;

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
    public function __construct(ReflectionMethod $reflection)
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
        return $this->reflection->getDocblock()->getShortDescription();
    }

    /**
     * Retrieve method long description
     * 
     * @return string
     */
    public function getLongDescription()
    {
        return $this->reflection->getDocblock()->getLongDescription();
    }

    /**
     * Retrieve method return type
     * 
     * @return string
     */
    public function getReturnType()
    {
        $return = $this->reflection->getDocblock()->getTag('return');

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

        $reflectionParams = $this->getParameterAnnotations();

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

        foreach ($values as $index => $value) {
            // Is it an internal type?
            if (in_array(strtolower($value), $this->internalTypes)) {
                continue;
            }

            // Does it match the class name?
            if ($value == $this->getClass()) {
                $values[$index] = $this->getNamespace() . '\\' . $value;
                continue;
            }

            $values[$index] = $this->resolveClass($value);
        }

        return implode('|', $values);
    }

    /**
     * Attempt to resolve a class or namespace based on imports
     * 
     * @param  string $class 
     * @return string String namespace/classname
     */
    protected function resolveClass($class)
    {
        if ('\\' == substr($class, 0, 1)) {
            return substr($class, 1);
        }

        foreach ($this->getUses() as $import) {
            // check for an "as". 
            if (isset($import['as'])) {
                $as = $import['as'];

                // If it matches $class exactly, use the "use" value provided
                if ($as == $class) {
                    return $import['use'];
                }

                // If the first portion of $class matches, then resolve with 
                // "use\\(classname - as)"
                $initialSegment = substr($class, 0, (strlen($as) + 1));
                if ($as . '\\' == $initialSegment) {
                    return $import['use'] . '\\' . substr($class, (strlen($as) + 1));
                }

                // Otherwise, we know this is not a match
                continue;
            }

            // get final segment of namespace provided in "use"
            $use = $import['use'];
            if (false === strstr($use, '\\')) {
                $finalSegment = $use;
            } else {
                $finalSegment = substr($use, strrpos($use, '\\') + 1);
            }
            // if class === final segment, return full "use"
            if ($class == $finalSegment) {
                return $use;
            }

            // if initial segment of class === final segment, return use + (class - initial segment)
            if (strstr($class, '\\')) {
                $initialSegment = substr($class, 0, strpos($class, '\\'));
                if ($finalSegment == $initialSegment) {
                    return $use . '\\' . substr($class, strpos($class, '\\') + 1);
                }
            }

            // Did not match... move to next
        }

        // check to see if a class by this name exists in the current namespace
        // if so, resolve to "namespace\\classname"
        $resolved = $this->getNamespace() . '\\' . $class;
        if (class_exists($resolved)) {
            return $resolved;
        }

        // Did not resolve; consider it fully resolved
        return $class;
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

        $class     = $r->getName();
        $namespace = $r->getNamespaceName();

        $class = substr($class, strlen($namespace) + 1);

        $this->class     = $class;
        $this->namespace = $namespace;

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
    protected function getParameterAnnotations()
    {
        if (null !== $this->parameterAnnotations) {
            return $this->parameterAnnotations;
        }

        $rDocblock = $this->reflection->getDocblock();
        $params    = $rDocblock->getTags('param');

        $this->parameterAnnotations = $params;
        return $this->parameterAnnotations;
    }
}
