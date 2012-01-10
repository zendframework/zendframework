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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Application\Resource;

use Zend\Log as ZendLog;

/**
 * Resource for initializing the locale
 *
 * @uses       \Zend\Application\Resource\AbstractResource
 * @uses       \Zend\Log\Logger
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Log extends AbstractResource
{
    /**
     * @var \Zend\Log\Logger
     */
    protected $_log;

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return \Zend\Log\Logger
     */
    public function init()
    {
        return $this->getLog();
    }

    /**
     * Attach logger
     * 
     * @param  \Zend\Log\Logger $log 
     * @return \Zend\Application\Resource\Log
     */
    public function setLog(ZendLog\Logger $log)
    {
        $this->_log = $log;
        return $this;
    }

    /**
     * Retrieve logger
     * 
     * @return \Zend\Log\Logger
     */
    public function getLog()
    {
        if (null === $this->_log) {
            $options = $this->getOptions();
            $log = ZendLog\Logger::factory($options);
            $this->setLog($log);
        }
        return $this->_log;
    }
}
