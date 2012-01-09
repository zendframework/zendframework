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
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Admin;

/**
 * Mock file for testbed
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class BazController extends \Zend\Controller\Action
{

    /**
     * Test Function for PreDispatch
     *
     * @return void
     */
    public function preDispatch()
    {
        $this->_response->appendBody("preDispatch called\n");
    }

    /**
     * Test Function for postDispatch
     *
     * @return void
     */
    public function postDispatch()
    {
        $this->_response->appendBody("postDispatch called\n");
    }

    /**
     * Test Function for barAction
     *
     * @return void
     */
    public function barAction()
    {
        $this->_response->appendBody("Admin's Baz::bar action called\n");
    }

}
