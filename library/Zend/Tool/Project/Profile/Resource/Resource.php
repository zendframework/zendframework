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
namespace Zend\Tool\Project\Profile\Resource;

/**
 * This class is an iterator that will iterate only over enabled resources
 *
 * @uses       \Zend\Tool\Project\Context\Repository
 * @uses       \Zend\Tool\Project\Exception
 * @uses       \Zend\Tool\Project\Profile\Resource\Container
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Resource extends Container
{

    /**
     * @var \Zend\Tool\Project\Profile\Profile
     */
    protected $_profile = null;

    /**
     * @var \Zend\Tool\Project\Profile\Resource\Resource
     */
    protected $_parentResource = null;

    /**#@+
     * @var bool
     */
    protected $_deleted = false;
    protected $_enabled = true;
    /**#@-*/

    /**
     * @var Zend\Tool\Project\Context|string
     */
    protected $_context = null;

    /**
     * @var array
     */
    protected $_attributes = array();

    /**
     * @var bool
     */
    protected $_isContextInitialized = false;

    /**
     * __construct()
     *
     * @param string|\Zend\Tool\Project\Context $context
     */
    public function __construct($context)
    {
        $this->setContext($context);
    }

    /**
     * setContext()
     *
     * @param string|\Zend\Tool\Project\Context $context
     * @return \Zend\Tool\Project\Profile\Resource\Resource
     */
    public function setContext($context)
    {
        $this->_context = $context;
        return $this;
    }

    /**
     * getContext()
     *
     * @return \Zend\Tool\Project\Context
     */
    public function getContext()
    {
        return $this->_context;
    }

    /**
     * getName() - Get the resource name
     *
     * Name is derived from the context name
     *
     * @return string
     */
    public function getName()
    {
        if (is_string($this->_context)) {
            return $this->_context;
        } elseif ($this->_context instanceof \Zend\Tool\Project\Context\Context) {
            return $this->_context->getName();
        } else {
            throw new \Zend\Tool\Project\Profile\Exception\InvalidArgumentException('Invalid context in resource');
        }
    }

    /**
     * setProfile()
     *
     * @param \Zend\Tool\Project\Profile\Profile $profile
     * @return \Zend\Tool\Project\Profile\Resource\Resource
     */
    public function setProfile(\Zend\Tool\Project\Profile\Profile $profile)
    {
        $this->_profile = $profile;
        return $this;
    }

    /**
     * getProfile
     *
     * @return \Zend\Tool\Project\Profile\Profile
     */
    public function getProfile()
    {
        return $this->_profile;
    }

    /**
     * getPersistentAttributes()
     *
     * @return array
     */
    public function getPersistentAttributes()
    {
        if (method_exists($this->_context, 'getPersistentAttributes')) {
            return $this->_context->getPersistentAttributes();
        }

        return array();
    }

    /**
     * setEnabled()
     *
     * @param bool $enabled
     * @return \Zend\Tool\Project\Profile\Resource\Resource
     */
    public function setEnabled($enabled = true)
    {
        // convert fuzzy types to bool
        $this->_enabled = (!in_array($enabled, array('false', 'disabled', 0, -1, false), true)) ? true : false;
        return $this;
    }

    /**
     * isEnabled()
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_enabled;
    }

    /**
     * setDeleted()
     *
     * @param bool $deleted
     * @return \Zend\Tool\Project\Profile\Resource\Resource
     */
    public function setDeleted($deleted = true)
    {
        $this->_deleted = (bool) $deleted;
        return $this;
    }

    /**
     * isDeleted()
     *
     * @return \Zend\Tool\Project\Profile\Resource\Resource
     */
    public function isDeleted()
    {
        return $this->_deleted;
    }

    /**
     * initializeContext()
     *
     * @return \Zend\Tool\Project\Profile\Resource\Resource
     */
    public function initializeContext()
    {
        if ($this->_isContextInitialized) {
            return;
        }
        if (is_string($this->_context)) {
            $this->_context = \Zend\Tool\Project\Context\Repository::getInstance()->getContext($this->_context);
        }

        if (method_exists($this->_context, 'setResource')) {
            $this->_context->setResource($this);
        }

        if (method_exists($this->_context, 'init')) {
            $this->_context->init();
        }

        $this->_isContextInitialized = true;
        return $this;
    }

    /**
     * __toString()
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_context->getName();
    }

    /**
     * __call()
     *
     * @param string $method
     * @param array $arguments
     * @return \Zend\Tool\Project\Profile\Resource\Resource
     */
    public function __call($method, $arguments)
    {
        if (method_exists($this->_context, $method)) {
            if (!$this->isEnabled()) {
                $this->setEnabled(true);
            }
            return call_user_func_array(array($this->_context, $method), $arguments);
        } else {
            throw new \Zend\Tool\Project\Profile\Exception('cannot call ' . $method);
        }
    }

}
