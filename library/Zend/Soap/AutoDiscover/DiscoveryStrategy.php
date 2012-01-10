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
 * @package    Zend_Soap
 * @subpackage WSDL
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Soap\AutoDiscover;

use Zend\Server\Reflection\AbstractFunction,
    Zend\Server\Reflection\Prototype,
    Zend\Server\Reflection\ReflectionParameter;

/**
 * Describes how types, return values and method details are detected during AutoDiscovery of a WSDL.
 *
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage WSDL
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface DiscoveryStrategy
{
    /**
     * Get the function parameters php type.
     *
     * Default implementation assumes the default param doc-block tag.
     *
     * @param ReflectionParameter $param
     * @return string
     */
    public function getFunctionParameterType(ReflectionParameter $param);

    /**
     * Get the functions return php type.
     *
     * Default implementation assumes the value of the return doc-block tag.
     *
     * @param AbstractFunction $function
     * @param Prototype $prototype
     * @return string
     */
    public function getFunctionReturnType(AbstractFunction $function, Prototype $prototype);

    /**
     * Detect if the function is a one-way or two-way operation.
     *
     * Default implementation assumes one-way, when return value is "void".
     *
     * @param AbstractFunction $function
     * @param Prototype $prototype
     * @return bool
     */
    public function isFunctionOneWay(AbstractFunction $function, Prototype $prototype);

    /**
     * Detect the functions documentation.
     *
     * Default implementation uses docblock description.
     *
     * @param AbstractFunction $function
     * @return string
     */
    public function getFunctionDocumentation(AbstractFunction $function);
}
