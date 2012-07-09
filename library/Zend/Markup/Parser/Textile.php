<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Markup
 */

namespace Zend\Markup\Parser;

use Zend\Markup;

/**
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Parser
 */
class Textile implements ParserInterface
{

    /**
     * Constructor
     *
     * @param \Zend\Config\Config|array $options
     */
    public function __construct($options = array())
    {
        // TODO: Implement __construct() method.
    }

    /**
     * Parse a string
     *
     * @todo IMPLEMENT
     *
     * @param  string $value
     *
     * @return array
     */
    public function parse($value)
    {
    }

    /**
     * Build a tree with a certain strategy
     *
     * @todo IMPLEMENT
     * @param array $tokens
     * @param string $strategy
     *
     * @return \Zend\Markup\TokenList
     */
    public function buildTree(array $tokens, $strategy = 'default')
    {
    }

    /**
     * Tokenize a string
     *
     * @param string $value
     *
     * @todo IMPLEMENT
     * @return array
     */
    public function tokenize($value)
    {
    }

}
