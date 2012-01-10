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
 * @package    Zend_Markup
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Markup\Renderer\Markup;

use Zend\Markup\Renderer\Markup\Html\Replace as ReplaceMarkup,
    Zend\Markup\Token;

/**
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage UnitTests
 * @group      Zend_Markup
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ReplaceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test the HTML replace markup
     *
     * @return void
     */
    public function testDefaultRendering()
    {
        $markup = new ReplaceMarkup('strong');

        $token = new Token('strong', Token::TYPE_MARKUP, 'strong', array());

        $token->setStopper('endstrong');

        $this->assertEquals('<strong>foo</strong>', $markup($token, 'foo'));
    }

    /**
     * Test the default HTML filtering
     *
     * @return void
     */
    public function testFilter()
    {
        $markup = new ReplaceMarkup('strong');

        $token = new Token('strong', Token::TYPE_MARKUP, 'strong', array());

        $token->setStopper('endstrong');

        $this->assertEquals('foo&lt;bar&gt;', $markup->filter('foo<bar>'));
        $this->assertEquals("foo<br />\nbar", $markup->filter("foo\nbar"));
        $this->assertEquals("foo<br />\n&lt;bar&gt;", $markup->filter("foo\n<bar>"));
    }
}
