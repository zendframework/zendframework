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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Markup\Renderer\Markup;

use Zend\Markup\Renderer\Markup\Html\Code as CodeMarkup,
    Zend\Markup\Token;

/**
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage UnitTests
 * @group      Zend_Markup
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class CodeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test the HTML code markup
     *
     * @return void
     */
    public function testDefaultRendering()
    {
        $code = new CodeMarkup();

        $token = new Token('codestart', Token::TYPE_MARKUP, 'code', array());

        $token->setStopper('codeend');

        $this->assertEquals(highlight_string('foo', true), $code($token, 'foo'));
        $this->assertEquals(highlight_string('<?= "bar" ?>', true), $code($token, '<?= "bar" ?>'));
        $this->assertEquals(highlight_string('<?php foobar(); ?>', true), $code($token, '<?php foobar(); ?>'));
    }
}
