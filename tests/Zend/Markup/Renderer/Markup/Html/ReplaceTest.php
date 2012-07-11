<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Markup
 */

namespace ZendTest\Markup\Renderer\Markup;

use Zend\Markup\Renderer\Markup\Html\Replace as ReplaceMarkup;
use Zend\Markup\Token;

/**
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage UnitTests
 * @group      Zend_Markup
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
