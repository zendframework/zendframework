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


/**
 * Mock file for testbed
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class HelperBrokerController extends \Zend\Controller\Action
{

    /**
     * Test Function for testGetRedirectorAction
     *
     * @return void
     */
    public function testGetRedirectorAction()
    {
        $redirector = $this->_helper->getHelper('Redirector');
        $this->getResponse()->appendBody(get_class($redirector));
    }

    /**
     * Test Function for testHelperViaMagicGetAction
     *
     * @return void
     */
    public function testHelperViaMagicGetAction()
    {
        $redirector = $this->_helper->Redirector;
        $this->getResponse()->appendBody(get_class($redirector));
    }

    /**
     * Test Function for testHelperViaMagicCallAction
     *
     * @return void
     */
    public function testHelperViaMagicCallAction()
    {
        $this->getResponse()->appendBody($this->_helper->TestHelper());
    }

    /**
     * Test Function for testBadHelperAction
     *
     * @return void
     */
    public function testBadHelperAction()
    {
        try {
            $this->_helper->getHelper('NonExistentHelper');
        } catch (\Exception $e) {
            $this->getResponse()->appendBody($e->getMessage());
        }
    }

    /**
     * Test Function for testCustomHelperAction
     *
     * @return void
     */
    public function testCustomHelperAction()
    {
        $this->getResponse()->appendBody(get_class($this->_helper->TestHelper));
    }

    /**
     * Test function for testLoadingHelperOnlyInitializesOnce
     * 
     * @return void
     */
    public function test()
    {
        $this->_helper->getHelper('testHelper');
    }
}
