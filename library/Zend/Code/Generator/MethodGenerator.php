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
 * @package    Zend_CodeGenerator
 * @subpackage PHP
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Code\Generator;

use Zend\Code\Reflection\MethodReflection;

/**
 * @uses       \Zend\Code\GeneratorDocblock
 * @uses       \Zend\Code\Generator\Exception
 * @uses       \Zend\Code\Generator\PhpMember\AbstractMember
 * @uses       \Zend\Code\Generator\PhpParameter
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class MethodGenerator extends AbstractMemberGenerator
{
    /**
     * @var DocblockGenerator
     */
    protected $docblock = null;

    /**
     * @var bool
     */
    protected $isFinal = false;

    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * @var string
     */
    protected $body = null;

    /**
     * fromReflection()
     *
     * @param \Zend\Code\Reflection\MethodReflection $reflectionMethod
     * @return \MethodGenerator\Code\Generator\PhpMethod
     */
    public static function fromReflection(MethodReflection $reflectionMethod)
    {
        $method = new self();

        $method->setSourceContent($reflectionMethod->getContents(false));
        $method->setSourceDirty(false);

        if ($reflectionMethod->getDocComment() != '') {
            $method->setDocblock(DocblockGenerator::fromReflection($reflectionMethod->getDocblock()));
        }

        $method->setFinal($reflectionMethod->isFinal());

        if ($reflectionMethod->isPrivate()) {
            $method->setVisibility(self::VISIBILITY_PRIVATE);
        } elseif ($reflectionMethod->isProtected()) {
            $method->setVisibility(self::VISIBILITY_PROTECTED);
        } else {
            $method->setVisibility(self::VISIBILITY_PUBLIC);
        }

        $method->setStatic($reflectionMethod->isStatic());

        $method->setName($reflectionMethod->getName());

        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            $method->setParameter(ParameterGenerator::fromReflection($reflectionParameter));
        }

        $method->setBody($reflectionMethod->getBody());

        return $method;
    }

    public function __construct($name = null, array $parameters = array(), $flags = self::FLAG_PUBLIC, $body = null, $docblock = null)
    {
        if ($name !== null) {
            $this->setName($name);
        }
        if ($parameters !== array()) {
            $this->setParameters($parameters);
        }
        if ($flags !== self::FLAG_PUBLIC) {
            $this->setFlags($flags);
        }
        if ($body !== null) {
            $this->setBody($body);
        }
        if ($docblock !== null) {
            $this->setDocblock($docblock);
        }
    }

    /**
     * setParameters()
     *
     * @param array $parameters
     * @return \MethodGenerator\Code\Generator\PhpMethod
     */
    public function setParameters(array $parameters)
    {
        foreach ($parameters as $parameter) {
            $this->setParameter($parameter);
        }
        return $this;
    }

    /**
     * setParameter()
     *
     * @param ParameterGenerator|string $parameter
     * @return \MethodGenerator\Code\Generator\PhpMethod
     */
    public function setParameter($parameter)
    {
        if (is_string($parameter)) {
            $parameter = new ParameterGenerator($parameter);
        } elseif (!$parameter instanceof ParameterGenerator) {
            throw new Exception\InvalidArgumentException('setParameter() expects either an array of method options or an instance of Zend_CodeGenerator_Php_Parameter');
        }
        $parameterName = $parameter->getName();

        $this->parameters[$parameterName] = $parameter;
        return $this;
    }

    /**
     * getParameters()
     *
     * @return array Array of \Zend\Code\Generator\Parameter\Parameter
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * setBody()
     *
     * @param string $body
     * @return \MethodGenerator\Code\Generator\PhpMethod
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * getBody()
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * generate()
     *
     * @return string
     */
    public function generate()
    {
        $output = '';

        $indent = $this->getIndentation();

        if (($docblock = $this->getDocblock()) !== null) {
            $docblock->setIndentation($indent);
            $output .= $docblock->generate();
        }

        $output .= $indent;

        if ($this->isAbstract()) {
            $output .= 'abstract ';
        } else {
            $output .= (($this->isFinal()) ? 'final ' : '');
        }

        $output .= $this->getVisibility()
            . (($this->isStatic()) ? ' static' : '')
            . ' function ' . $this->getName() . '(';

        $parameters = $this->getParameters();
        if (!empty($parameters)) {
            foreach ($parameters as $parameter) {
                $parameterOuput[] = $parameter->generate();
            }

            $output .= implode(', ', $parameterOuput);
        }

        $output .= ')' . self::LINE_FEED . $indent . '{' . self::LINE_FEED;

        if ($this->body) {
            $output .= preg_replace('#^(.+?)$#m', $indent . $indent . '$1', trim($this->body))
                    .  self::LINE_FEED;
        }

        $output .= $indent . '}' . self::LINE_FEED;

        return $output;
    }

    public function __toString()
    {
        return $this->generate();
    }

}
