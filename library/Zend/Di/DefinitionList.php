<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Di
 */

namespace Zend\Di;

use SplDoublyLinkedList;

/**
 * Class definition based on multiple definitions
 *
 * @category   Zend
 * @package    Zend_Di
 */
class DefinitionList extends SplDoublyLinkedList implements Definition\DefinitionInterface
{

    public function __construct($definitions)
    {
        if (!is_array($definitions)) {
            $definitions = array($definitions);
        }
        foreach ($definitions as $definition) {
            $this->push($definition);
        }
    }

    /**
     * Add definitions
     *
     * @param  Definition\DefinitionInterface $definition
     * @param  bool                           $addToBackOfList
     * @return void
     */
    public function addDefinition(Definition\DefinitionInterface $definition, $addToBackOfList = true)
    {
        if ($addToBackOfList) {
            $this->push($definition);
        } else {
            $this->unshift($definition);
        }
    }

    /**
     * @param  string       $type
     * @return Definition[]
     */
    public function getDefinitionsByType($type)
    {
        $definitions = array();
        foreach ($this as $definition) {
            if ($definition instanceof $type) {
                $definitions[] = $definition;
            }
        }

        return $definitions;
    }

    /**
     * Get definition by type
     *
     * @param  string                         $type
     * @return Definition\DefinitionInterface
     */
    public function getDefinitionByType($type)
    {
        foreach ($this as $definition) {
            if ($definition instanceof $type) {
                return $definition;
            }
        }

        return false;
    }

    public function getDefinitionForClass($class)
    {
        /** @var $definition Definition\DefinitionInterface */
        foreach ($this as $definition) {
            if ($definition->hasClass($class)) {
                return $definition;
            }
        }

        return false;
    }

    public function forClass($class)
    {
        return $this->getDefinitionForClass($class);
    }

    /**
     * Get classes
     *
     * @return array
     */
    public function getClasses()
    {
        $classes = array();
        /** @var $definition Definition\DefinitionInterface */
        foreach ($this as $definition) {
            $classes = array_merge($classes, $definition->getClasses());
        }

        return $classes;
    }

    /**
     * Check for class
     *
     * @param  string $class
     * @return bool
     */
    public function hasClass($class)
    {
        /** @var $definition Definition\DefinitionInterface */
        foreach ($this as $definition) {
            if ($definition->hasClass($class)) {
                return true;
            }
        }

        return false;
    }

    public function getClassSupertypes($class)
    {
        $supertypes = array();
        /** @var $definition Definition\DefinitionInterface */
        foreach ($this as $definition) {
            $supertypes = array_merge($supertypes, $definition->getClassSupertypes($class));
        }
        // @todo remove duplicates?
        return $supertypes;
    }

    public function getInstantiator($class)
    {
        /** @var $definition Definition\DefinitionInterface */
        foreach ($this as $definition) {
            if ($definition->hasClass($class)) {
                $value = $definition->getInstantiator($class);
                if ($value === null && $definition instanceof Definition\PartialMarker) {
                    continue;
                } else {
                    return $value;
                }
            }
        }

        return false;
    }

    /**
     * Check for methods
     *
     * @param  string $class
     * @return bool
     */
    public function hasMethods($class)
    {
        /** @var $definition Definition\DefinitionInterface */
        foreach ($this as $definition) {
            if ($definition->hasClass($class)) {
                if ($definition->hasMethods($class) === false && $definition instanceof Definition\PartialMarker) {
                    continue;
                } else {
                    return $definition->hasMethods($class);
                }
            }
        }

        return false;
    }

    /**
     * Check for method
     *
     * @param  string $class
     * @param  string $method
     * @return bool
     */
    public function hasMethod($class, $method)
    {
        /** @var $definition Definition\DefinitionInterface */
        foreach ($this as $definition) {
            if ($definition->hasClass($class)) {
                if ($definition->hasMethods($class) === false && $definition instanceof Definition\PartialMarker) {
                    continue;
                } else {
                    return $definition->hasMethods($class);
                }
            }
        }

        return false;
    }

    /**
     * Get methods
     *
     * @param  string $class
     * @return array|
     */
    public function getMethods($class)
    {
        /** @var $definition Definition\DefinitionInterface */
        $methods = array();
        foreach ($this as $definition) {
            if ($definition->hasClass($class)) {
                if ($definition instanceof Definition\PartialMarker) {
                    $methods = array_merge($definition->getMethods($class), $methods);
                } else {
                    return array_merge($definition->getMethods($class), $methods);
                }
            }
        }

        return $methods;
    }

    public function hasMethodParameters($class, $method)
    {
        $methodParameters = $this->getMethodParameters($class, $method);

        return ($methodParameters !== array());
    }

    public function getMethodParameters($class, $method)
    {
        /** @var $definition Definition\DefinitionInterface */
        foreach ($this as $definition) {
            if ($definition->hasClass($class) && $definition->hasMethod($class, $method) && $definition->hasMethodParameters($class, $method)) {
                return $definition->getMethodParameters($class, $method);
            }
        }

        return array();
    }

}
