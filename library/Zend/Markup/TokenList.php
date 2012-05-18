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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Markup;

/**
 * @category   Zend
 * @package    Zend_Markup
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class TokenList implements \RecursiveIterator
{

    /**
     * Array of tokens
     *
     * @var array
     */
    protected $_tokens = array();

    /**
     * Get the current token
     *
     * @return \Zend\Markup\Token
     */
    public function current()
    {
        return current($this->_tokens);
    }

    /**
     * Get the children of the current token
     *
     * @return \Zend\Markup\TokenList
     */
    public function getChildren()
    {
        return current($this->_tokens)->getChildren();
    }

    /**
     * Add a new child token
     *
     * @param \Zend\Markup\Token $child
     *
     * @return void
     */
    public function addChild(Token $child)
    {
        $this->_tokens[] = $child;
    }

    /**
     * Check if the current token has children
     *
     * @return bool
     */
    public function hasChildren()
    {
        return current($this->_tokens)->hasChildren();
    }

    /**
     * Get the key of the current token
     *
     * @return int
     */
    public function key()
    {
        return key($this->_tokens);
    }

    /**
     * Go to the next token
     *
     * @return \Zend\Markup\Token
     */
    public function next()
    {
        return next($this->_tokens);
    }

    /**
     * Rewind the iterator
     *
     * @return void
     */
    public function rewind()
    {
        reset($this->_tokens);
    }

    /**
     * Check if the element is valid
     *
     * @return bool
     */
    public function valid()
    {
        return $this->current() !== false;
    }
}
