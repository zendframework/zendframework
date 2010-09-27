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
namespace ZendTest\Markup\Parser;

use Zend\Markup\Parser,
    Zend\Markup\Parser\Bbcode;

/**
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage UnitTests
 * @group      Zend_Markup
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class BbcodeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The BBCode parser
     *
     * @var \Zend\Markup\Parser\Bbcode
     */
    protected $_parser;


    /**
     * Setup the testcase
     *
     * @return void
     */
    public function setUp()
    {
        $this->_parser = new Bbcode();
    }

    /**
     * Tear the testcase down
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->_parser);
    }

    /**
     * Test the tokenizer with a simple tag
     *
     * @return void
     */
    public function testTokenizerSimpleTag()
    {
        $this->assertEquals(array(
            array(
                'tag'  => 'foo',
                'type' => 'none'
            ),
            array(
                'tag'        => '[b]',
                'name'       => 'b',
                'attributes' => array(),
                'type'       => 'markup'
            ),
            array(
                'tag'  => 'bar',
                'type' => 'none'
            ),
            array(
                'tag'        => '[/b]',
                'name'       => '/b',
                'attributes' => array(),
                'type'       => 'markup'
            )
        ), $this->_parser->tokenize('foo[b]bar[/b]'));
    }

    /**
     * Test the tokenizer with a complicated tag
     *
     * @return void
     */
    public function testTokenizerComplicatedTag()
    {
        $this->assertEquals(array(
            array(
                'tag'  => 'foo',
                'type' => 'none'
            ),
            array(
                'tag'        => '[bar=foo a="b^c" d=\']\']',
                'name'       => 'bar',
                'attributes' => array(
                    'bar' => 'foo',
                    'a'   => 'b^c',
                    'd'   => ']'
                ),
                'type'       => 'markup'
            ),
            array(
                'tag'  => 'bar',
                'type' => 'none'
            ),
            array(
                'tag'        => '[/b]',
                'name'       => '/b',
                'attributes' => array(),
                'type'       => 'markup'
            )
        ), $this->_parser->tokenize('foo[bar=foo a="b^c" d=\']\']bar[/b]'));
    }

    /**
     * Test the tokenizer with nested (simple) tags
     *
     * @return void
     */
    public function testTokenizerNestedTags()
    {
        $this->assertEquals(array(
            array(
                'tag'  => 'foo',
                'type' => 'none'
            ),
            array(
                'tag'        => '[b]',
                'name'       => 'b',
                'attributes' => array(),
                'type'       => 'markup'
            ),
            array(
                'tag'  => 'bar',
                'type' => 'none'
            ),
            array(
                'tag'        => '[i]',
                'name'       => 'i',
                'attributes' => array(),
                'type'       => 'markup'
            ),
            array(
                'tag'  => 'baz',
                'type' => 'none'
            ),
            array(
                'tag'        => '[abc]',
                'name'       => 'abc',
                'attributes' => array(),
                'type'       => 'markup'
            ),
            array(
                'tag'  => 'caz',
                'type' => 'none'
            ),
            array(
                'tag'        => '[/abc]',
                'name'       => '/abc',
                'attributes' => array(),
                'type'       => 'markup'
            ),
            array(
                'tag'  => 'naz',
                'type' => 'none'
            ),
            array(
                'tag'        => '[/i]',
                'name'       => '/i',
                'attributes' => array(),
                'type'       => 'markup'
            ),
            array(
                'tag'  => 'booh',
                'type' => 'none'
            ),
            array(
                'tag'        => '[/b]',
                'name'       => '/b',
                'attributes' => array(),
                'type'       => 'markup'
            )
        ), $this->_parser->tokenize('foo[b]bar[i]baz[abc]caz[/abc]naz[/i]booh[/b]'));
    }
}
