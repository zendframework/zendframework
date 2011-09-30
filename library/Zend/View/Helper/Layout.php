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
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\View\Helper;

/**
 * View helper for retrieving layout object
 *
 * @uses       \Zend\Layout\Layout
 * @uses       \Zend\View\Helper\AbstractHelper
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Layout extends AbstractHelper
{
    /** @var \Zend\Layout\Layout */
    protected $_layout;

    /**
     * Get layout object
     *
     * @return \Zend\Layout\Layout
     */
    public function getLayout()
    {
        if (null === $this->_layout) {
            $this->_layout = \Zend\Layout\Layout::getMvcInstance();
            if (null === $this->_layout) {
                // Implicitly creates layout object
                $this->_layout = new \Zend\Layout\Layout();
            }
        }

        return $this->_layout;
    }

    /**
     * Set layout object
     *
     * @param  \Zend\Layout\Layout $layout
     * @return \Zend\Layout\Controller\Action\Helper\Layout
     */
    public function setLayout(\Zend\Layout\Layout $layout)
    {
        $this->_layout = $layout;
        return $this;
    }

    /**
     * Return layout object
     *
     * Usage: $this->layout()->setLayout('alternate');
     *
     * @return \Zend\Layout\Layout
     */
    public function __invoke()
    {
        return $this->getLayout();
    }
}
