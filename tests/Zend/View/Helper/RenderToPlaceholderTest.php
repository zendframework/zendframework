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

/**
 * @namespace
 */
namespace ZendTest\View\Helper;

use Zend\View\Renderer\PhpRenderer as View,
    Zend\View\Helper\Placeholder as PlaceholderHelper;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
        $placeholder = new PlaceholderHelper();
        $this->assertEquals("Foo Bar\n", $placeholder->__invoke('fooPlaceholder')->getValue());
    }

}

