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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Tool\Framework\Action;
use Zend\Tool\Framework\Action;

/**
 * @uses       \Zend\Tool\Framework\Action
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Base implements Action
{

    /**
     * @var string
     */
    protected $_name = null;

    /**
     * constructor -
     *
     * @param unknown_type $options
     */
    public function __construct($options = null)
    {
        if ($options !== null) {
            if (is_string($options)) {
                $this->setName($options);
            }
            // implement $options here in the future if this is needed
        }
    }

    /**
     * setName()
     *
     * @param string $name
     * @return \Zend\Tool\Framework\Action\Base
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        if ($this->_name == null) {
            $this->_name = $this->_parseName();
        }
        return $this->_name;
    }

    /**
     * _parseName - internal method to determine the name of an action when one is not explicity provided.
     *
     * @param \Zend\Tool\Framework\Action $action
     * @return string
     */
    protected function _parseName()
    {
        $className = get_class($this);
        $actionName = substr($className, strrpos($className, '\\')+1);
        return $actionName;
    }

}
