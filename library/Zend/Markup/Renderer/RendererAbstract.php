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
 * @subpackage Renderer
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Markup\Renderer;
use Zend\Markup;
use Zend\Markup\Parser;
use Zend\Markup\Renderer\Markup\MarkupInterface;

/**
 * Defines the basic rendering functionality
 *
 * @uses       \Zend\Markup\Renderer\Exception
 * @uses       \Zend\Markup\Renderer\Markup\MarkupInterface
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class RendererAbstract
{

    /**
     * Markup info
     *
     * @var array
     */
    protected $_markups = array();

    /**
     * Parser
     *
     * @var \Zend\Markup\Parser\ParserInterface
     */
    protected $_parser;

    /**
     * The current group
     *
     * @var string
     */
    protected $_group;

    /**
     * Groups definition
     *
     * @var array
     */
    protected $_groups = array();

    /**
     * Plugin loader for markups
     *
     * @var \Zend\Loader\PluginLoader\PluginLoader
     */
    protected $_pluginLoader;

    /**
     * The current token
     *
     * @var \Zend\Markup\Token
     */
    protected $_token;

    /**
     * Encoding
     *
     * @var string
     */
    protected $_encoding = 'UTF-8';


    /**
     * Constructor
     *
     * @param array|\Zend\Config\Config $options
     *
     * @todo make constructor compliant with new configuration standards
     *
     * @return void
     */
    public function __construct($options = array())
    {
        if ($options instanceof \Zend\Config\Config) {
            $options = $options->toArray();
        }

        if (isset($options['encoding'])) {
            $this->setEncoding($options['encoding']);
        }
        if (isset($options['parser'])) {
            $this->setParser($options['parser']);
        }
    }

    /**
     * Set the parser
     *
     * @param  \Zend\Markup\Parser\ParserInterface $parser
     *
     * @return \Zend\Markup\Renderer\RendererAbstract
     */
    public function setParser(Parser\ParserInterface $parser)
    {
        $this->_parser = $parser;

        return $this;
    }

    /**
     * Get the parser
     *
     * @return \Zend\Markup\Parser\ParserInterface
     */
    public function getParser()
    {
        return $this->_parser;
    }

    /**
     * Get the plugin loader
     *
     * @return \Zend\Loader\PluginLoader\PluginLoader
     */
    public function getPluginLoader()
    {
        return $this->_pluginLoader;
    }

    /**
     * Set the renderer's encoding
     *
     * @param string $encoding
     *
     * @return \Zend\Markup\Renderer\RendererAbstract
     */
    public function setEncoding($encoding)
    {
        $this->_encoding = $encoding;

        return $this;
    }

    /**
     * Get the renderer's encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->_encoding;
    }

    /**
     * Add a new markup
     *
     * @param string $name
     * @param \Zend\Markup\Renderer\Markup\MarkupInterface $markup
     *
     * @return \Zend\Markup\Renderer\RendererAbstract
     */
    public function addMarkup($name, MarkupInterface $markup)
    {
        $this->_markups[$name] = $markup;

        return $this;
    }

    /**
     * Remove a markup
     *
     * @param string $name
     *
     * @return void
     */
    public function removeMarkup($name)
    {
        unset($this->_markups[$name]);
    }

    /**
     * Remove all the markups
     *
     * @return void
     */
    public function clearMarkups()
    {
        $this->_markups = array();
    }

    /**
     * Render function
     *
     * @param  \Zend\Markup\TokenList|string $tokenList
     *
     * @return string
     */
    public function render($value)
    {
        if ($value instanceof Markup\TokenList) {
            $tokenList = $value;
        } else {
            $tokenList = $this->getParser()->parse($value);
        }

        $root = $tokenList->current();

        return $this->_render($root);
    }

    /**
     * Render a single token
     *
     * @param  \Zend\Markup\Token $token
     * @return string
     */
    protected function _render(Markup\Token $token)
    {
        $return = '';

        $this->_token = $token;

        // if this markup has children, execute them
        if ($token->hasChildren()) {
            foreach ($token->getChildren() as $child) {
                $return .= $this->_execute($child);
            }
        }

        return $return;
    }

    /**
     * Execute the token
     *
     * @param  \Zend\Markup\Token $token
     *
     * @return string
     */
    protected function _execute(Markup\Token $token)
    {
        switch ($token->getType()) {
            case Markup\Token::TYPE_MARKUP:
                if (!isset($this->_markups[$token->getName()])) {
                    // TODO: apply filters
                    return $token->getContent() . $this->_render($token) . $token->getStopper();
                }

                $markup = $this->_markups[$token->getName()];

                return $markup($token, $this->_render($token));
                break;
            case Markup\Token::TYPE_NONE:
            default:
                // TODO: apply filters
                return $token->getContent();
                break;
        }
    }

    /**
     * Add a render group
     *
     * @param string $name
     * @param array $allowedInside
     * @param array $allowsInside
     *
     * @return void
     */
    public function addGroup($name, array $allowedInside = array(), array $allowsInside = array())
    {
        $this->_groups[$name] = $allowsInside;

        foreach ($allowedInside as $group) {
            $this->_groups[$group][] = $name;
        }
    }

    /**
     * Get group definitions
     *
     * @return array
     */
    public function getGroups()
    {
        return $this->_groups;
    }
}
