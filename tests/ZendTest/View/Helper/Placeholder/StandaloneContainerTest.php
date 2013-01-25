<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Helper\Placeholder;

use Zend\View\Renderer\PhpRenderer as View;

/**
 * Test class for Zend_View_Helper_Placeholder_StandaloneContainer.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class StandaloneContainerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        \Zend\View\Helper\Placeholder\Registry::unsetRegistry();
        $this->basePath = __DIR__ . '/_files/modules';
        $this->helper = new Foo();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->helper);
    }

    public function testViewAccessorWorks()
    {
        $view = new View();
        $this->helper->setView($view);
        $this->assertSame($view, $this->helper->getView());
    }

    public function testContainersPersistBetweenInstances()
    {
        $foo1 = new Foo;
        $foo1->append('Foo');
        $foo1->setSeparator(' - ');

        $foo2 = new Foo;
        $foo2->append('Bar');

        $test = $foo1->toString();
        $this->assertContains('Foo', $test);
        $this->assertContains(' - ', $test);
        $this->assertContains('Bar', $test);
    }
}

class Foo extends \Zend\View\Helper\Placeholder\Container\AbstractStandalone
{
    protected $_regKey = 'foo';
    public function direct() {}
}
