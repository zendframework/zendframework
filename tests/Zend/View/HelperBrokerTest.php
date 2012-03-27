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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\View;

use Zend\View\HelperBroker,
    Zend\View\Renderer\PhpRenderer;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 */
class HelperBrokerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->broker = new HelperBroker();
    }

    public function testUsesHelperLoaderAsDefaultClassLoader()
    {
        $this->assertInstanceOf('Zend\View\HelperLoader', $this->broker->getClassLoader());
    }

    public function testViewIsNullByDefault()
    {
        $this->assertNull($this->broker->getView());
    }

    public function testAllowsPassingRendererForView()
    {
        $renderer = new PhpRenderer();
        $this->broker->setView($renderer);
        $this->assertSame($renderer, $this->broker->getView());
    }

    public function testInjectsRendererToHelperWhenRendererIsPresent()
    {
        $renderer = new PhpRenderer();
        $this->broker->setView($renderer);
        $helper = $this->broker->load('doctype');
        $this->assertSame($renderer, $helper->getView());
    }

    public function testNoRendererInjectedInHelperWhenRendererIsNotPresent()
    {
        $helper = $this->broker->load('doctype');
        $this->assertNull($helper->getView());
    }

    public function testRegisteringInvalidHelperRaisesException()
    {
        $this->setExpectedException('Zend\View\Exception\InvalidHelperException');
        $this->broker->register('test', $this);
    }

    public function testLoadingInvalidHelperRaisesException()
    {
        $this->broker->getClassLoader()->registerPlugin('test', get_class($this));
        $this->setExpectedException('Zend\View\Exception\InvalidHelperException');
        $this->broker->register('test', $this);
    }
}
