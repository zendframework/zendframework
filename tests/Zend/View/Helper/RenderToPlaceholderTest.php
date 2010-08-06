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
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\View\Helper;


/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class RenderToPlaceholderTest extends \PHPUnit_Framework_TestCase
{

    protected $_view = null;

    public function setUp()
    {
        $this->_view = new \Zend\View\View(array('scriptPath'=>__DIR__.'/_files/scripts/'));
    }

    public function testDefaultEmpty()
    {
        $this->_view->renderToPlaceholder('rendertoplaceholderscript.phtml', 'fooPlaceholder');
        $placeholder = new \Zend\View\Helper\Placeholder();
        $this->assertEquals("Foo Bar\n", $placeholder->direct('fooPlaceholder')->getValue());
    }

}

