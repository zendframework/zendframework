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

use ReflectionMethod as PhpReflectionMethod,
    Zend\Code\Reflection,
    Zend\Code\Annotation,
    Zend\Code\Scanner\CachingFileScanner,
    Zend\Code\Scanner\AnnotationScanner;

/**
 * @uses       ReflectionMethod
 * @uses       Zend\Code\Reflection\ReflectionClass
 * @uses       Zend_Reflection_Docblock
 * @uses       Zend\Code\Reflection\Exception
 * @uses       Zend\Code\Reflection\ReflectionParameter
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class MethodReflection extends PhpReflectionMethod implements Reflection
{

    /**
     * @var AnnotationCollection
     */
    protected $annotations = null;

    /**
     * Retrieve method docblock reflection
     *
     * @return DocBlockReflection|false
     */
    public function getDocBlock()
    {
        if ('' == $this->getDocComment()) {
            return false;
        }

        $instance = new DocBlockReflection($this);
        return $instance;
    }

    /**
     * @param \Zend\Code\Annotation\AnnotationManager $annotationManager
     * @return \Zend\Code\Annotation\AnnotationCollection
     */
    public function getAnnotations(Annotation\AnnotationManager $annotationManager)
    {
        if (($docComment = $this->getDocComment()) == '') {
            return false;
        }

        if (!$this->annotations) {
            $cachingFileScanner = new CachingFileScanner($this->getFileName());
            $nameInformation = $cachingFileScanner->getClassNameInformation($this->getDeclaringClass()->getName());

            $this->annotations = new AnnotationScanner($annotationManager, $docComment, $nameInformation);
        }

        return $this->annotations;
    }

    /**
     * Get start line (position) of method
     *
     * @param  bool $includeDocComment
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
     * Get reflection of declaring class
     *
     * @param  string $reflectionClass Name of reflection class to use
     * @return ClassReflection
     */
    public function getDeclaringClass()
    {
        $phpReflection  = parent::getDeclaringClass();
        $zendReflection = new ClassReflection($phpReflection->getName());
        unset($phpReflection);
        return $zendReflection;
    }

    /**
     * Get all method parameter reflection objects
     *
     * @param  string $reflectionClass Name of reflection class to use
     * @return array of \Zend\Code\Reflection\ReflectionParameter objects
     */
    public function getParameters()
    {
        $phpReflections  = parent::getParameters();
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $instance = new ParameterReflection(array($this->getDeclaringClass()->getName(), $this->getName()), $phpReflection->getName());
            $zendReflections[] = $instance;
            unset($phpReflection);
        }
        unset($phpReflections);
        return $zendReflections;
    }

    /**
     * Get method contents
     *
     * @param  bool $includeDocblock
     * @return string
     */
    public function getContents($includeDocblock = true)
    {
        $fileContents = file($this->getFileName());
        $startNum = $this->getStartLine($includeDocblock);
        $endNum = ($this->getEndLine() - $this->getStartLine());

        return implode("\n", array_splice($fileContents, $startNum, $endNum, true));
    }

    /**
     * Get method body
     *
     * @return string
     */
    public function getBody()
    {
        $lines = array_slice(
            file($this->getDeclaringClass()->getFileName(), FILE_IGNORE_NEW_LINES),
            $this->getStartLine(),
            ($this->getEndLine() - $this->getStartLine()),
            true
        );

        $firstLine = array_shift($lines);

        if (trim($firstLine) !== '{') {
            array_unshift($lines, $firstLine);
        }

        $lastLine = array_pop($lines);

        if (trim($lastLine) !== '}') {
            array_push($lines, $lastLine);
        }

        // just in case we had code on the bracket lines
        return rtrim(ltrim(implode("\n", $lines), '{'), '}');
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
