<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Helper;

use PHPUnit_Framework_TestCase as TestCase;
use ZendTest\View\Helper\TestAsset\ConcreteHelper;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class AbstractTest extends TestCase
{
    /**
     * @var ConcreteHelper
     */
    protected $helper;

    public function setUp()
    {
        $this->helper = new ConcreteHelper();
    }

    public function testViewSettersGetters()
    {
        $viewMock = $this->getMock('Zend\View\Renderer\RendererInterface');

        $this->helper->setView($viewMock);
        $this->assertEquals($viewMock, $this->helper->getView());
    }
}
