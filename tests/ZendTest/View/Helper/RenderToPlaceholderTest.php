<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\View\Helper;

use Zend\View\Renderer\PhpRenderer as View;

/**
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class RenderToPlaceholderTest extends \PHPUnit_Framework_TestCase
{
    protected $_view = null;

    public function setUp()
    {
        $this->_view = new View();
        $this->_view->resolver()->addPath(__DIR__.'/_files/scripts/');
    }

    public function testDefaultEmpty()
    {
        $this->_view->plugin('renderToPlaceholder')->__invoke('rendertoplaceholderscript.phtml', 'fooPlaceholder');
        $placeholder = $this->_view->plugin('placeholder');
        $this->assertEquals("Foo Bar" . "\n", $placeholder->__invoke('fooPlaceholder')->getValue());
    }
}
