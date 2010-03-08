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
 * @see Zend_Markup_TokenList
 */

/**
 * @see Zend_Markup_Parser_ParserInterface
 */

/**
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Parser
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Markup_Test_Parser_MockParser implements Zend_Markup_Parser_ParserInterface
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
            throw new Zend_Markup_Parser_Exception('Value to parse should be a string.');
        }

        if (empty($value)) {
            /**
             * @see Zend_Markup_Parser_Exception
             */
            throw new Zend_Markup_Parser_Exception('Value to parse cannot be left empty.');
        }

        // initialize variables
        $tree    = new Zend_Markup_TokenList();
        $current = new Zend_Markup_Token(
            '',
            Zend_Markup_Token::TYPE_NONE,
            'Zend_Markup_Root'
        );

        $tree->addChild($current);

        $token = new Zend_Markup_Token(
            $value,
            Zend_Markup_Token::TYPE_NONE,
            '',
            array(),
            $current
        );
        $current->addChild($token);

        return $tree;
    }
}
