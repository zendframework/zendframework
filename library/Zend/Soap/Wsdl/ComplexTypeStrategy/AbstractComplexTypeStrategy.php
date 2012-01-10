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
namespace Zend\Soap\Wsdl\ComplexTypeStrategy;

use Zend\Soap\Wsdl\ComplexTypeStrategy;

/**
 * Abstract class for Zend_Soap_Wsdl_Strategy.
 *
 * @uses       \Zend\Soap\Wsdl\Strategy
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage WSDL
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractComplexTypeStrategy implements ComplexTypeStrategy
{
    /**
     * Context object
     *
     * @var \Zend\Soap\Wsdl
     */
    protected $_context;

    /**
     * Set the Zend_Soap_Wsdl Context object this strategy resides in.
     *
     * @param \Zend\Soap\Wsdl $context
     * @return void
     */
    public function setContext(\Zend\Soap\Wsdl $context)
    {
        $this->_context = $context;
    }

    /**
     * Return the current Zend_Soap_Wsdl context object
     *
     * @return \Zend\Soap\Wsdl
     */
    public function getContext()
    {
        return $this->_context;
    }

    /**
     * Look through registered types
     *
     * @param string $phpType
     * @return string
     */
    public function scanRegisteredTypes($phpType)
    {
        if (array_key_exists($phpType, $this->getContext()->getTypes())) {
            $soapTypes = $this->getContext()->getTypes();
            return $soapTypes[$phpType];
        }

        return null;
    }
}
