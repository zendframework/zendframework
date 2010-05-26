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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Soap\WSDL\Strategy;

/**
 * Abstract class for Zend_Soap_WSDL_Strategy.
 *
 * @uses       \Zend\Soap\WSDL\Strategy\StrategyInterface
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage WSDL
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractStrategy implements StrategyInterface
{
    /**
     * Context object
     *
     * @var \Zend\Soap\WSDL\WSDL
     */
    protected $_context;

    /**
     * Set the Zend_Soap_WSDL Context object this strategy resides in.
     *
     * @param \Zend\Soap\WSDL\WSDL $context
     * @return void
     */
    public function setContext(\Zend\Soap\WSDL\WSDL $context)
    {
        $this->_context = $context;
    }

    /**
     * Return the current Zend_Soap_WSDL context object
     *
     * @return \Zend\Soap\WSDL\WSDL
     */
    public function getContext()
    {
        return $this->_context;
    }
}
