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
 * @package    Zend_Tool
 * @subpackage Framework
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Tool\Framework\Metadata;

/**
 * @uses       \Zend\Tool\Framework\Metadata\Basic
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Tool extends Basic
{

    /**
     * @var string
     */
    protected $_type = 'Tool';

    /**#@+
     * @var string
     */
    protected $_clientName    = null;
    protected $_actionName    = null;
    protected $_providerName  = null;
    protected $_specialtyName = null;
    /**#@-*/

    /**#@+
     * @var string
     */
    protected $_clientReference = null;
    protected $_actionReference = null;
    protected $_providerReference = null;
    /**#@-*/

    public function setClientName($clientName)
    {
        $this->_clientName = $clientName;
        return $this;
    }

    public function getClientName()
    {
        return $this->_clientName;
    }

    /**
     * setActionName()
     *
     * @param string $actionName
     * @return \Zend\Tool\Framework\Metadata\Tool
     */
    public function setActionName($actionName)
    {
        $this->_actionName = $actionName;
        return $this;
    }

    /**
     * getActionName()
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->_actionName;
    }

    /**
     * setProviderName()
     *
     * @param string $providerName
     * @return \Zend\Tool\Framework\Metadata\Tool
     */
    public function setProviderName($providerName)
    {
        $this->_providerName = $providerName;
        return $this;
    }

    /**
     * getProviderName()
     *
     * @return string
     */
    public function getProviderName()
    {
        return $this->_providerName;
    }

    /**
     * setSpecialtyName()
     *
     * @param string $specialtyName
     * @return \Zend\Tool\Framework\Metadata\Tool
     */
    public function setSpecialtyName($specialtyName)
    {
        $this->_specialtyName = $specialtyName;
        return $this;
    }

    /**
     * getSpecialtyName()
     *
     * @return string
     */
    public function getSpecialtyName()
    {
        return $this->_specialtyName;
    }

    /**
     * setClientReference()
     *
     * @param \Zend\Tool\Framework\Client\AbstractClient $client
     * @return \Zend\Tool\Framework\Metadata\Tool
     */
    public function setClientReference(\Zend\Tool\Framework\Client\AbstractClient $client)
    {
        $this->_clientReference = $client;
        return $this;
    }

    /**
     * getClientReference()
     *
     * @return \Zend\Tool\Framework\Client\AbstractClient
     */
    public function getClientReference()
    {
        return $this->_clientReference;
    }

    /**
     * setActionReference()
     *
     * @param \Zend\Tool\Framework\Action $action
     * @return \Zend\Tool\Framework\Metadata\Tool
     */
    public function setActionReference(\Zend\Tool\Framework\Action $action)
    {
        $this->_actionReference = $action;
        return $this;
    }

    /**
     * getActionReference()
     *
     * @return \Zend\Tool\Framework\Action
     */
    public function getActionReference()
    {
        return $this->_actionReference;
    }

    /**
     * setProviderReference()
     *
     * @param \Zend\Tool\Framework\Provider $provider
     * @return \Zend\Tool\Framework\Metadata\Tool
     */
    public function setProviderReference(\Zend\Tool\Framework\Provider $provider)
    {
        $this->_providerReference = $provider;
        return $this;
    }

    /**
     * getProviderReference()
     *
     * @return \Zend\Tool\Framework\Provider
     */
    public function getProviderReference()
    {
        return $this->_providerReference;
    }

    /**
     * __toString() cast to string
     *
     * @return string
     */
    public function __toString()
    {
        $string = parent::__toString();
        $string .= ' (ProviderName: ' . $this->_providerName
             . ', ActionName: '     . $this->_actionName
             . ', SpecialtyName: '  . $this->_specialtyName
             . ')';

        return $string;
    }

}
