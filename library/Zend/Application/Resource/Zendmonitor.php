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
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Application_Resource_Zendmonitor extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @var Zend_Log
     */
    protected $_log;

    /**
     * Initialize log resource
     * 
     * @return Zend_Log
     */
    public function init()
    {
        return $this->getLog();
    }

    /**
     * Get log instance
     *
     * Lazy-loads instance if not registered
     * 
     * @return Zend_Log
     */
    public function getLog()
    {
        if (null === $this->_log) {
            $this->setLog(new Zend_Log(new Zend_Log_Writer_ZendMonitor()));
        }
        return $this->_log;
    }

    /**
     * Set log instance
     * 
     * @param  Zend_Log $log 
     * @return Zend_Application_Resource_Zendmonitor
     */
    public function setLog(Zend_Log $log)
    {
        $this->_log = $log;
        return $this;
    }
}
