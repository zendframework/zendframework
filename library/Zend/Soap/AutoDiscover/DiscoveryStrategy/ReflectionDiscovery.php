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
namespace Zend\Soap\AutoDiscover\DiscoveryStrategy;

use Zend\Soap\AutoDiscover\DiscoveryStrategy,
    Zend\Server\Reflection\AbstractFunction,
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

class ReflectionDiscovery implements DiscoveryStrategy
{
    public function getFunctionDocumentation(AbstractFunction $function)
    {
        return $function->getDescription();
    }

    public function getFunctionParameterType(ReflectionParameter $param)
    {
        return $param->getType();
    }

    public function getFunctionReturnType(AbstractFunction $function, Prototype $prototype)
    {
        return $prototype->getReturnType();
    }

    public function isFunctionOneWay(AbstractFunction $function, Prototype $prototype)
    {
        return $prototype->getReturnType() == 'void';
    }
}
