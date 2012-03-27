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

/**
 * Resource for settings layout options
 *
 * @uses       \Zend\Application\Resource\AbstractResource
 * @uses       \Zend\Layout\Layout
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Layout
    extends AbstractResource
{
    /**
     * @var \Zend\Layout\Layout
     */
    protected $_layout;

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return \Zend\Layout\Layout
     */
    public function init()
    {
        $this->getBootstrap()->bootstrap('frontcontroller');
        return $this->getLayout();
    }

    /**
     * Retrieve layout object
     *
     * @return \Zend\Layout\Layout
     */
    public function getLayout()
    {
        if (null === $this->_layout) {
            $this->_layout = \Zend\Layout\Layout::startMvc($this->getOptions());
        }
        return $this->_layout;
    }
}
