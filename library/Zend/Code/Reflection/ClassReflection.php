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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Code\Reflection;

use Zend\Code\Reflection,
    ReflectionClass,
    Zend\Code\Reflection\FileReflection,
    Zend\Code\Scanner\FileScanner,
    Zend\Code\Annotation,
    Zend\Code\Scanner\AnnotationScanner;

/**
 * @uses       ReflectionClass
 * @uses       Zend_Reflection_Docblock
 * @uses       \Zend\Code\Reflection\Exception
 * @uses       \Zend\Code\Reflection\ReflectionMethod
 * @uses       \Zend\Code\Reflection\ReflectionProperty
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ClassReflection extends ReflectionClass implements Reflection
{

    /**
     * @var Annotation\AnnotationCollection
     */
    protected $annotations = null;


    /**
     * Return the reflection file of the declaring file.
     *
     * @return FileReflection
     */
    public function getDeclaringFile()
    {
        $instance = new FileReflection($this->getFileName());
        return $instance;
    }

    /**
     * Return the classes Docblock reflection object
     *
     * @return DocBlockReflection
     * @throws \Zend\Code\Reflection\Exception for missing docblock or invalid reflection class
     */
    public function getDocBlock()
    {
        if ('' == $this->getDocComment()) {
            return false;
        }

        $instance = new DocBlockReflection($this->getDocComment());
        return $instance;
    }

    /**
     * @return AnnotationCollection
     */
    public function getAnnotations(Annotation\AnnotationManager $annotationManager)
    {
        if (($docComment = $this->getDocComment()) == '') {
            return false;
        }

        if (!$this->annotations) {
            $fileScanner = new FileScanner($this->getFileName());
            $nameInformation = $fileScanner->getClassNameInformation($this->getName());
            $this->annotations = new AnnotationScanner($annotationManager, $docComment, $nameInformation);
        }

        return $this->annotations;
    }

    /**
     * Return the start line of the class
     *
     * @param bool $includeDocComment
     * @return int
     */
    public function getStartLine($includeDocComment = false)
    {
        if ($includeDocComment && $this->getDocComment() != '') {
            return $this->getDocBlock()->getStartLine();
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
     * @return array Array of \Zend\Code\Reflection\ReflectionClass
     */
    public function getInterfaces()
    {
        $phpReflections  = parent::getInterfaces();
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $instance = new ClassReflection($phpReflection->getName());
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
     * @return \MethodReflection\Code\Reflection\ReflectionMethod
     */
    public function getMethod($name)
    {
        $method = new MethodReflection($this->getName(), parent::getMethod($name)->getName());
        return $method;
    }

    /**
     * Get reflection objects of all methods
     *
     * @param  string $filter
     * @return MethodReflection[]
     */
    public function getMethods($filter = -1)
    {
        $methods = array();
        foreach (parent::getMethods($filter) as $method) {
            $instance = new MethodReflection($this->getName(), $method->getName());
            $methods[] = $instance;
        }
        return $methods;
    }

    /**
     * Get parent reflection class of reflected class
     *
     * @return \Zend\Code\Reflection\ReflectionClass
     */
    public function getParentClass()
    {
        $phpReflection = parent::getParentClass();
        if ($phpReflection) {
            $zendReflection = new ClassReflection($phpReflection->getName());
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
     * @return \PropertyReflection\Code\Reflection\ReflectionProperty
     */
    public function getProperty($name)
    {
        $phpReflection  = parent::getProperty($name);
        $zendReflection = new PropertyReflection($this->getName(), $phpReflection->getName());
        unset($phpReflection);
        return $zendReflection;
    }

    /**
     * Return reflection properties of this class
     *
     * @param  int $filter
     * @return array Array of \Zend\Code\Reflection\ReflectionProperty
     */
    public function getProperties($filter = -1)
    {
        $phpReflections = parent::getProperties($filter);
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $instance = new PropertyReflection($this->getName(), $phpReflection->getName());
            $zendReflections[] = $instance;
            unset($phpReflection);
        }
        unset($phpReflections);
        return $zendReflections;
    }

    public function toString()
    {
        return parent::__toString();
    }

    public function __toString()
    {
        return parent::__toString();
    }

}

