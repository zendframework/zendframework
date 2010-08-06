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
 * @subpackage Parser
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Markup\TestAsset\Parser;

use Zend\Markup\Parser,
    Zend\Markup\Token,
    Zend\Markup\TokenList;

/**
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Parser
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class MockParser implements Parser
{

    /**
     * Parse a string
     *
     * @param  string $value
     * @return Zend_Markup_TokenList
     */
    public function parse($value)
    {
        if (!is_string($value)) {
            /**
             * @see Zend_Markup_Parser_Exception
             */
            throw new Parser\Exception('Value to parse should be a string.');
        }

        if (empty($value)) {
            /**
             * @see Zend_Markup_Parser_Exception
             */
            throw new Parser\Exception('Value to parse cannot be left empty.');
        }

        // initialize variables
        $tree    = new TokenList();
        $current = new Token(
            '',
            Token::TYPE_NONE,
            'Zend_Markup_Root'
        );

        $tree->addChild($current);

        $token = new Token(
            $value,
            Token::TYPE_NONE,
            '',
            array(),
            $current
        );
        $current->addChild($token);

        return $tree;
    }

    public function buildTree(array $tokens, $strategy = 'default')
    {
    }

    public function tokenize($value)
    {
    }
}
