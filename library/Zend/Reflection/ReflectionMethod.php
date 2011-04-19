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
namespace Zend\Reflection;

/**
 * @uses       ReflectionMethod
 * @uses       Zend\Reflection\ReflectionClass
 * @uses       Zend_Reflection_Docblock
 * @uses       Zend\Reflection\Exception
 * @uses       Zend\Reflection\ReflectionParameter
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ReflectionMethod extends \ReflectionMethod
{
    /**
     * Retrieve method docblock reflection
     *
     * @return Zend_Reflection_Docblock
     * @throws \Zend\Reflection\Exception
     */
    public function getDocblock($reflectionClass = 'Zend\Reflection\ReflectionDocblock')
    {
        if ('' == $this->getDocComment()) {
            throw new Exception\InvalidArgumentException($this->getName() . ' does not have a docblock');
        }

        $instance = new $reflectionClass($this);
        if (!$instance instanceof ReflectionDocblock) {
            throw new Exception\InvalidArgumentException('Invalid reflection class provided; must extend Zend\Reflection\ReflectionDocblock');
        }
        return $instance;
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
     * @return \Zend\Reflection\ReflectionClass
     */
    public function getDeclaringClass($reflectionClass = 'Zend\Reflection\ReflectionClass')
    {
        $phpReflection  = parent::getDeclaringClass();
        $zendReflection = new $reflectionClass($phpReflection->getName());
        if (!$zendReflection instanceof ReflectionClass) {
            throw new Exception\InvalidArgumentException('Invalid reflection class provided; must extend Zend\Reflection\ReflectionClass');
        }
        unset($phpReflection);
        return $zendReflection;
    }

    /**
     * Get all method parameter reflection objects
     *
     * @param  string $reflectionClass Name of reflection class to use
     * @return array of \Zend\Reflection\ReflectionParameter objects
     */
    public function getParameters($reflectionClass = 'Zend\Reflection\ReflectionParameter')
    {
        $phpReflections  = parent::getParameters();
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $instance = new $reflectionClass(array($this->getDeclaringClass()->getName(), $this->getName()), $phpReflection->getName());
            if (!$instance instanceof ReflectionParameter) {
                throw new Exception\InvalidArgumentException('Invalid reflection class provided; must extend Zend\Reflection\ReflectionParameter');
            }
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
}
