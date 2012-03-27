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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Markup\Renderer;
use Zend\Markup\Token,
    Zend\Markup\TokenList,
    Zend\Markup\Parser,
    Zend\Markup\Renderer\Markup,
    Zend\Config\Config;

/**
 * Defines the basic rendering functionality
 *
 * @uses       \Zend\Markup\Renderer\Exception
 * @uses       \Zend\Markup\Renderer\Markup\MarkupInterface
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractRenderer
{

    /**
     * Markup info
     *
     * @var array
     */
    protected $_markups = array();

    /**
     * The current markup
     *
     * @var Markup
     */
    protected $_markup;

    /**
     * Parser
     *
     * @var \Zend\Markup\Parser
     */
    protected $_parser;

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
        if ($options instanceof Config) {
            $options = $options->toArray();
        }

        if (isset($options['encoding'])) {
            $this->setEncoding($options['encoding']);
        }
        if (isset($options['parser'])) {
            $this->setParser($options['parser']);
        }
        if (isset($options['markups'])) {
            $this->addMarkups($options['markups']);
        }
    }

    /**
     * Set the parser
     *
     * @param  \Zend\Markup\Parser $parser
     *
     * @return \Zend\Markup\Renderer\RendererAbstract
     */
    public function setParser(Parser $parser)
    {
        $this->_parser = $parser;

        return $this;
    }

    /**
     * Get the parser
     *
     * @return \Zend\Markup\Parser
     */
    public function getParser()
    {
        return $this->_parser;
    }

    /**
     * Set the renderer's encoding
     *
     * @param string $encoding
     *
     * @return \Zend\Markup\Renderer\AbstractRenderer
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
     * Add multiple markups
     *
     * @param array $markups
     *
     * @return AbstractRenderer
     */
    public function addMarkups(array $markups)
    {
        foreach ($markups as $name => $markup) {
            $this->addMarkup($name, $markup);
        }
    }

    /**
     * Add a new markup
     *
     * @param string $name
     * @param Markup $markup
     *
     * @return AbstractRenderer
     */
    public function addMarkup($name, Markup $markup)
    {
        $markup->setRenderer($this);

        $this->_markups[$name] = $markup;

        return $this;
    }

    /**
     * Get a markup
     *
     * @param string $name
     *
     * @throws Exception\RuntimeException if the markup doesn't exist
     *
     * @return Markup
     */
    public function getMarkup($name)
    {
        if (!isset($this->_markups[$name])) {
            throw new Exception\RuntimeException("The markup with name '$name' doesn't exist");
        }

        return $this->_markups[$name];
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
     * @throws Exception\RuntimeException when there is no root markup given
     * @return string
     */
    public function render($value)
    {
        if (!isset($this->_markups['Zend_Markup_Root'])) {
            throw new Exception\RuntimeException("There is no Zend_Markup_Root markup.");
        }

        if ($value instanceof TokenList) {
            $tokenList = $value;
        } else {
            $tokenList = $this->getParser()->parse($value);
        }

        $root = $tokenList->current();

        // set the default markup
        $this->_markup = $this->_markups['Zend_Markup_Root'];

        return $this->_render($root);
    }

    /**
     * Render a single token
     *
     * @param  \Zend\Markup\Token $token
     * @return string
     */
    protected function _render(Token $token)
    {
        $return = '';

        $oldToken     = $this->_token;
        $this->_token = $token;

        // if this markup has children, execute them
        if ($token->hasChildren()) {
            foreach ($token->getChildren() as $child) {
                $return .= $this->_execute($child);
            }
        }

        $this->_token = $oldToken;

        return $return;
    }

    /**
     * Execute the token
     *
     * @param  \Zend\Markup\Token $token
     *
     * @return string
     */
    protected function _execute(Token $token)
    {
        switch ($token->getType()) {
            case Token::TYPE_MARKUP:
                if (!isset($this->_markups[$token->getName()])) {
                    // TODO: apply filters
                    return $this->_markup->filter($token->getContent())
                         . $this->_render($token)
                         . $this->_markup->filter($token->getStopper());
                }

                $markup = $this->_markups[$token->getName()];

                // change the rendering environiment
                $oldMarkup     = $this->_markup;
                $this->_markup = $markup;

                $value = $markup($token, $this->_render($token));

                // and change the rendering environiment back
                $this->_markup = $oldMarkup;

                return $value;
                break;
            case Token::TYPE_NONE:
            default:
                return $this->_markup->filter($token->getContent());
                break;
        }
    }
}
