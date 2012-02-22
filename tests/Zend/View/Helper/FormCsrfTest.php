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
 * @package    Zendview
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\View\Helper;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\View\Renderer\PhpRenderer as View,
    Zend\View\Helper\FormCsrf;

/**
 * @category   Zend
 * @package    Zendview
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zendview
 * @group      Zendview_Helper
 */
class FormCsrfTest extends TestCase
{
    /**
     * @var FormCsrf
     */
    protected $helper;

    /**
     * @var View
     */
    protected $view;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->helper = new FormCsrf();
        $this->view   = new View();
        $this->view->doctype()->setDoctype(strtoupper("XHTML1_STRICT"));
        $this->helper->setView($this->view);

        if (isset($_SERVER['HTTPS'])) {
            unset ($_SERVER['HTTPS']);
        }
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        unset($this->helper, $this->view);
    }

    public function testCsrfXhtmlDoctype()
    {
        $this->assertRegExp(
            '/\/>$/',
            $this->helper->__invoke()
        );
    }

    public function testCsrfHtmlDoctype()
    {
        $object = new FormCsrf();
        $view   = new View();
        $view->doctype()->setDoctype(strtoupper("HTML5"));
        $object->setView($view);

        $this->assertRegExp(
            '/[^\/]>$/',
            $object->__invoke()
        );
    }

    public function testReturnInputTag()
    {
        $this->assertRegExp(
            "/^<input\s.+/",
            $this->helper->__invoke()
        );
    }
}
