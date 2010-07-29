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
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\View\Helper;

/**
 * Zend_View_Helper_UrlTest
 *
 * Tests formText helper, including some common functionality of all form helpers
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class UrlTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->front = \Zend\Controller\Front::getInstance();
        $this->front->getRouter()->addDefaultRoutes();

        // $this->view = new Zend_View();
        $this->helper = new \Zend\View\Helper\Url();
        // $this->helper->setView($this->view);
    }

    public function testDefaultEmpty()
    {
        $url = $this->helper->direct();
        $this->assertEquals('/', $url);
    }

    public function testDefault()
    {
        $url = $this->helper->direct(array('controller' => 'ctrl', 'action' => 'act'));
        $this->assertEquals('/ctrl/act', $url);
    }

}
