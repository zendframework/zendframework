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

use Zend\Layout\Layout as BaseLayout;

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
    /** @var BaseLayout */
    protected $_layout;

    /**
     * Get layout object
     *
     * @return BaseLayout
     */
    public function getLayout()
    {
        if (null === $this->_layout) {
            $this->_layout = new BaseLayout();
        }

        return $this->_layout;
    }

    /**
     * Set layout object
     *
     * @param  BaseLayout $layout
     * @return Layout
     */
    public function setLayout(BaseLayout $layout)
    {
        $this->_layout = $layout;
        return $this;
    }

    /**
     * Return layout object
     *
     * @return BaseLayout
     */
    public function __invoke()
    {
        return $this->getLayout();
    }
}
