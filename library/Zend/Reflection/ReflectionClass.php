<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Reflection;

/**
 * @uses       ReflectionClass
 * @uses       Zend_Reflection_Docblock
 * @uses       \Zend\Reflection\Exception
 * @uses       \Zend\Reflection\ReflectionMethod
 * @uses       \Zend\Reflection\ReflectionProperty
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ReflectionClass extends \ReflectionClass
{
    /**
     * Return the reflection file of the declaring file.
     *
     * @return \Zend\Reflection\ReflectionFile
     */
    public function getDeclaringFile($reflectionClass = '\Zend\Reflection\ReflectionFile')
    {
        $instance = new $reflectionClass($this->getFileName());
        if (!$instance instanceof ReflectionFile) {
            throw new Exception('Invalid reflection class specified; must extend Zend\Reflection\ReflectionFile');
        }
        return $instance;
    }

    /**
     * Return the classes Docblock reflection object
     *
     * @param  string $reflectionClass Name of reflection class to use
     * @return Zend_Reflection_Docblock
     * @throws \Zend\Reflection\Exception for missing docblock or invalid reflection class
     */
    public function getDocblock($reflectionClass = '\Zend\Reflection\ReflectionDocblock')
    {
        if ('' == $this->getDocComment()) {
            throw new Exception($this->getName() . ' does not have a docblock');
        }

        $instance = new $reflectionClass($this);
        if (!$instance instanceof ReflectionDocblock) {
            throw new Exception('Invalid reflection class specified; must extend Zend_Reflection_Docblock');
        }
        return $instance;
    }

    /**
     * Return the start line of the class
     *
     * @param bool $includeDocComment
     * @return int
     */
    public function getStartLine($includeDocComment = false)
    {
        if ($includeDocComment) {
            if ($this->getDocComment() != '') {
                return $this->getDocblock()->getStartLine();
            }
        }

        return parent::getStartLine();
    }

    /**
     * Return the contents of the class
     *
     * @param bool $includeDocblock
     * @return string
     */
    public function getContents($includeDocblock = true)
    {
        $filename  = $this->getFileName();
        $filelines = file($filename);
        $startnum  = $this->getStartLine($includeDocblock);
        $endnum    = $this->getEndLine() - $this->getStartLine();

        return implode('', array_splice($filelines, $startnum, $endnum, true));
    }

    /**
     * Get all reflection objects of implemented interfaces
     *
     * @param  string $reflectionClass Name of reflection class to use
     * @return array Array of \Zend\Reflection\ReflectionClass
     */
    public function getInterfaces($reflectionClass = '\Zend\Reflection\ReflectionClass')
    {
        $phpReflections  = parent::getInterfaces();
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $instance = new $reflectionClass($phpReflection->getName());
            if (!$instance instanceof ReflectionClass) {
                throw new Exception('Invalid reflection class specified; must extend Zend\Reflection\ReflectionClass');
            }
            $zendReflections[] = $instance;
            unset($phpReflection);
        }
        unset($phpReflections);
        return $zendReflections;
    }

    /**
     * Return method reflection by name
     *
     * @param  string $name
     * @param  string $reflectionClass Reflection class to utilize
     * @return \Zend\Reflection\ReflectionMethod
     */
    public function getMethod($name, $reflectionClass = '\Zend\Reflection\ReflectionMethod')
    {
        $phpReflection  = parent::getMethod($name);
        $zendReflection = new $reflectionClass($this->getName(), $phpReflection->getName());

        if (!$zendReflection instanceof ReflectionMethod) {
            throw new Exception('Invalid reflection class specified; must extend Zend\Reflection\ReflectionMethod');
        }

        unset($phpReflection);
        return $zendReflection;
    }

    /**
     * Get reflection objects of all methods
     *
     * @param  string $filter
     * @param  string $reflectionClass Reflection class to use for methods
     * @return array Array of \Zend\Reflection\ReflectionMethod objects
     */
    public function getMethods($filter = -1, $reflectionClass = '\Zend\Reflection\ReflectionMethod')
    {
        $phpReflections  = parent::getMethods($filter);
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $instance = new $reflectionClass($this->getName(), $phpReflection->getName());
            if (!$instance instanceof ReflectionMethod) {
                throw new Exception('Invalid reflection class specified; must extend Zend\Reflection\ReflectionMethod');
            }
            $zendReflections[] = $instance;
            unset($phpReflection);
        }
        unset($phpReflections);
        return $zendReflections;
    }

    /**
     * Get parent reflection class of reflected class
     *
     * @param  string $reflectionClass Name of Reflection class to use
     * @return \Zend\Reflection\ReflectionClass
     */
    public function getParentClass($reflectionClass = 'Zend\Reflection\ReflectionClass')
    {
        $phpReflection = parent::getParentClass();
        if ($phpReflection) {
            $zendReflection = new $reflectionClass($phpReflection->getName());
            if (!$zendReflection instanceof ReflectionClass) {
                throw new Exception('Invalid reflection class specified; must extend Zend\Reflection\ReflectionClass');
            }
            unset($phpReflection);
            return $zendReflection;
        } else {
            return false;
        }
    }

    /**
     * Return reflection property of this class by name
     *
     * @param  string $name
     * @param  string $reflectionClass Name of reflection class to use
     * @return \Zend\Reflection\ReflectionProperty
     */
    public function getProperty($name, $reflectionClass = '\Zend\Reflection\ReflectionProperty')
    {
        $phpReflection  = parent::getProperty($name);
        $zendReflection = new $reflectionClass($this->getName(), $phpReflection->getName());
        if (!$zendReflection instanceof ReflectionProperty) {
            throw new Exception('Invalid reflection class specified; must extend Zend\Reflection\ReflectionProperty');
        }
        unset($phpReflection);
        return $zendReflection;
    }

    /**
     * Return reflection properties of this class
     *
     * @param  int $filter
     * @param  string $reflectionClass Name of reflection class to use
     * @return array Array of \Zend\Reflection\ReflectionProperty
     */
    public function getProperties($filter = -1, $reflectionClass = '\Zend\Reflection\ReflectionProperty')
    {
        $phpReflections = parent::getProperties($filter);
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $instance = new $reflectionClass($this->getName(), $phpReflection->getName());
            if (!$instance instanceof ReflectionProperty) {
                throw new Exception('Invalid reflection class specified; must extend Zend\Reflection\ReflectionProperty');
            }
            $zendReflections[] = $instance;
            unset($phpReflection);
        }
        unset($phpReflections);
        return $zendReflections;
    }
}
