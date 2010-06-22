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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Application\Resource;

use Zend\Application\ResourceException,
    Zend\Session\SessionManager,
    Zend\Session\SaveHandler;

/**
 * Resource for setting session options
 *
 * @uses       \Zend\Application\ResourceException
 * @uses       \Zend\Application\Resource\AbstractResource
 * @uses       \Zend\Session\Manager
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Session extends AbstractResource
{
    /**
     * Save handler to use
     *
     * @var \Zend\Session\SaveHandler
     */
    protected $_saveHandler = null;

    /**
     * Set session save handler
     *
     * @param  array|string|\Zend\Session\SaveHandler $saveHandler
     * @return \Zend\Application\Resource\Session
     * @throws \Zend\Application\ResourceException When $saveHandler is no valid save handler
     */
    public function setSaveHandler($saveHandler)
    {
        $this->_saveHandler = $saveHandler;
        return $this;
    }

    /**
     * Get session save handler
     *
     * @return \Zend\Session\SaveHandler
     */
    public function getSaveHandler()
    {
        if (!$this->_saveHandler instanceof SaveHandler) {
            if (is_array($this->_saveHandler)) {
                if (!array_key_exists('class', $this->_saveHandler)) {
                    throw new ResourceException('Session save handler class not provided in options');
                }
                $options = array();
                if (array_key_exists('options', $this->_saveHandler)) {
                    $options = $this->_saveHandler['options'];
                }
                $this->_saveHandler = $this->_saveHandler['class'];
                $this->_saveHandler = new $this->_saveHandler($options);
            } elseif (is_string($this->_saveHandler)) {
                $this->_saveHandler = new $this->_saveHandler;
            }

            if (!$this->_saveHandler instanceof SaveHandler) {
                throw new ResourceException('Invalid session save handler');
            }
        }
        return $this->_saveHandler;
    }

    /**
     * @return bool
     */
    protected function _hasSaveHandler()
    {
        return ($this->_saveHandler !== null);
    }

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return void
     */
    public function init()
    {
        $options = array_change_key_case($this->getOptions(), CASE_LOWER);
        if (isset($options['savehandler'])) {
            unset($options['savehandler']);
        }

        $handler = false;
        if ($this->_hasSaveHandler()) {
            $handler = $this->getSaveHandler();
        }

        if (count($options) > 0) {
            $manager = new SessionManager($options);
            return $manager;
        }

        return ($handler) ? $handler : false;
    }
}
