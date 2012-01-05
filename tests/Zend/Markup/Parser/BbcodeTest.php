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
namespace ZendTest\Markup\Parser;

use Zend\Markup\Token,
    Zend\Markup\TokenList,
    Zend\Markup\Parser,
    Zend\Markup\Parser\Bbcode;

/**
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage UnitTests
 * @group      Zend_Markup
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
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
        $this->_parser = new Bbcode(array(
            'groups' => array(
                'block'       => array('block', 'blockignore', 'inline'),
                'inline'      => array('inline'),
                'blockignore' => array()
            ),
            'default_group' => 'block',
            'initial_group' => 'block',
            'tags' => array(
                'code' => array(
                    'group' => 'blockignore'
                )
            )
        ));
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

    /**
     * A simple test for the tree builder
     *
     * @return void
     */
    public function testBuildTreeSimple()
    {
        $input = array(
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
        );

        $tree = $this->_parser->buildTree($input);

        // first check the root
        $root = $tree->current();

        $this->assertEquals('Zend_Markup_Root', $root->getName());

        // now check the subtokens of the root
        $children = $root->getChildren();

        // the first one should be 'foo'
        $this->assertEquals('foo', $children->current()->getContent());
        $this->assertEquals(Token::TYPE_NONE, $children->current()->getType());

        // the second one should be the [b] tag
        $children->next();

        $b = $children->current();

        $this->assertEquals('b',                $b->getName());
        $this->assertEquals('[b]',              $b->getContent());
        $this->assertEquals('[/b]',             $b->getStopper());
        $this->assertEquals(array(),            $b->getAttributes());
        $this->assertEquals(Token::TYPE_MARKUP, $b->getType());

        // check the b tag's children
        $children = $b->getChildren();

        // the first child of b is bar
        $this->assertEquals('bar', $children->current()->getContent());
        $this->assertEquals(Token::TYPE_NONE, $children->current()->getType());
    }

    /**
     * Test if group definitions work with the treebuilder
     *
     * @return void
     */
    public function testGroups()
    {
        $input = array(
            array(
                'tag'        => '[code]',
                'name'       => 'code',
                'attributes' => array(),
                'type'       => 'markup'
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
            ),
            array(
                'tag'        => '[/code]',
                'name'       => '/code',
                'attributes' => array(),
                'type'       => 'markup'
            )
        );

        $tree = $this->_parser->buildTree($input);

        // first check the root
        $root = $tree->current();

        $this->assertEquals($root->getName(), 'Zend_Markup_Root');

        // now check the subtokens of the root
        $children = $root->getChildren();


        // the [code] tag
        $code = $children->current();

        $this->assertEquals('code',             $code->getName());
        $this->assertEquals('[code]',           $code->getContent());
        $this->assertEquals('[/code]',          $code->getStopper());
        $this->assertEquals(array(),            $code->getAttributes());
        $this->assertEquals(Token::TYPE_MARKUP, $code->getType());

        // check the code tag's children
        $children = $code->getChildren();

        // the first child of the code tag should have the content [b]
        // but in this case the [b] tag should not have been parsed as tag
        $this->assertEquals('[b]',            $children->current()->getContent());
        $this->assertEquals(Token::TYPE_NONE, $children->current()->getType());

        $this->assertNotNull($children->next());

        // now we just have simple content
        $this->assertEquals('bar', $children->current()->getContent());
        $this->assertEquals(Token::TYPE_NONE, $children->current()->getType());

        $this->assertNotNull($children->next());

        // and now we have the end of the [/b] tag, which shouldn't be parsed of course
        $this->assertEquals('[/b]', $children->current()->getContent());
        $this->assertEquals(Token::TYPE_NONE, $children->current()->getType());
    }
}
