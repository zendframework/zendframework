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
 * @see Zend_Uri
 */
require_once 'Zend/Uri.php';

/**
 * @see Zend_Markup_Renderer_RendererAbstract
 */
require_once 'Zend/Markup/Renderer/RendererAbstract.php';

/**
 * HTML renderer
 *
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Markup_Renderer_Html extends Zend_Markup_Renderer_RendererAbstract
{
    /**
     * Tag info
     *
     * @var array
     */
    protected $_tags = array(
        'b' => array(
            'type'   => 10, // self::TYPE_REPLACE | self::TAG_NORMAL
            'tag'    => 'strong',
            'group'  => 'inline',
            'filter' => true,
        ),
        'u' => array(
            'type'        => 10,
            'tag'         => 'span',
            'attributes'  => array(
                'style' => 'text-decoration: underline;',
            ),
            'group'       => 'inline',
            'filter'      => true,
        ),
        'i' => array(
            'type'   => 10,
            'tag'    => 'em',
            'group'  => 'inline',
            'filter' => true,
        ),
        'cite' => array(
            'type'   => 10,
            'tag'    => 'cite',
            'group'  => 'inline',
            'filter' => true,
        ),
        'del' => array(
            'type'   => 10,
            'tag'    => 'del',
            'group'  => 'inline',
            'filter' => true,
        ),
        'ins' => array(
            'type'   => 10,
            'tag'    => 'ins',
            'group'  => 'inline',
            'filter' => true,
        ),
        'sub' => array(
            'type'   => 10,
            'tag'    => 'sub',
            'group'  => 'inline',
            'filter' => true,
        ),
        'sup' => array(
            'type'   => 10,
            'tag'    => 'sup',
            'group'  => 'inline',
            'filter' => true,
        ),
        'span' => array(
            'type'   => 10,
            'tag'    => 'span',
            'group'  => 'inline',
            'filter' => true,
        ),
        'acronym'  => array(
            'type'   => 10,
            'tag'    => 'acronym',
            'group'  => 'inline',
            'filter' => true,
        ),
        // headings
        'h1' => array(
            'type'   => 10,
            'tag'    => 'h1',
            'group'  => 'inline',
            'filter' => false,
        ),
        'h2' => array(
            'type'   => 10,
            'tag'    => 'h2',
            'group'  => 'inline',
            'filter' => false,
        ),
        'h3' => array(
            'type'   => 10,
            'tag'    => 'h3',
            'group'  => 'inline',
            'filter' => false,
        ),
        'h4' => array(
            'type'   => 10,
            'tag'    => 'h4',
            'group'  => 'inline',
            'filter' => false,
        ),
        'h5' => array(
            'type'   => 10,
            'tag'    => 'h5',
            'group'  => 'inline',
            'filter' => false,
        ),
        'h6' => array(
            'type'   => 10,
            'tag'    => 'h6',
            'group'  => 'inline',
            'filter' => false,
        ),
        // callback tags
        'url' => array(
            'type'     => 6, // self::TYPE_CALLBACK | self::TAG_NORMAL
            'callback' => array('Zend_Markup_Renderer_Html', '_htmlUrl'),
            'group'    => 'inline',
            'filter'   => true,
        ),
        'img' => array(
            'type'     => 6,
            'callback' => array('Zend_Markup_Renderer_Html', '_htmlImg'),
            'group'    => 'inline_empty',
            'filter'   => true,
        ),
        'code' => array(
            'type'     => 6,
            'callback' => array('Zend_Markup_Renderer_Html', '_htmlCode'),
            'group'    => 'block_empty',
            'filter'   => false,
        ),
        'p' => array(
            'type'   => 10,
            'tag'    => 'p',
            'group'  => 'block',
            'filter' => true,
        ),
        'ignore' => array(
            'type'   => 10,
            'start'  => '',
            'end'    => '',
            'group'  => 'block_empty',
            'filter' => true,
        ),
        'quote' => array(
            'type'   => 10,
            'tag'    => 'blockquote',
            'group'  => 'block',
            'filter' => true,
        ),
        'list' => array(
            'type'     => 6,
            'callback' => array('Zend_Markup_Renderer_Html', '_htmlList'),
            'group'    => 'list',
            'filter'   => false,
        ),
        '*' => array(
            'type'   => 10,
            'tag'    => 'li',
            'group'  => 'list-item',
            'filter' => false,
        ),
        'hr' => array(
            'type'    => 9, // self::TYPE_REPLACE | self::TAG_SINGLE
            'tag'     => 'hr',
            'group'   => 'block',
        ),
        // aliases
        'bold' => array(
            'type' => 16,
            'name' => 'b',
        ),
        'strong' => array(
            'type' => 16,
            'name' => 'b',
        ),
        'italic' => array(
            'type' => 16,
            'name' => 'i',
        ),
        'em' => array(
            'type' => 16,
            'name' => 'i',
        ),
        'emphasized' => array(
            'type' => 16,
            'name' => 'i',
        ),
        'underline' => array(
            'type' => 16,
            'name' => 'u',
        ),
        'citation' => array(
            'type' => 16,
            'name' => 'cite',
        ),
        'deleted' => array(
            'type' => 16,
            'name' => 'del',
        ),
        'insert' => array(
            'type' => 16,
            'name' => 'ins',
        ),
        'strike' => array(
            'type' => 16,
            'name' => 's',
        ),
        's' => array(
            'type' => 16,
            'name' => 'del',
        ),
        'subscript' => array(
            'type' => 16,
            'name' => 'sub',
        ),
        'superscript' => array(
            'type' => 16,
            'name' => 'sup',
        ),
        'a' => array(
            'type' => 16,
            'name' => 'url',
        ),
        'image' => array(
            'type' => 16,
            'name' => 'img',
        ),
        'li' => array(
            'type' => 16,
            'name' => '*',
        ),
        'color' => array(
            'type' => 16,
            'name' => 'span'
        )
    );

    /**
     * Element groups
     *
     * @var array
     */
    protected $_groups = array(
        'block'        => array('block', 'inline', 'block_empty', 'inline_empty', 'list'),
        'inline'       => array('inline', 'inline_empty'),
        'list'         => array('list-item'),
        'list-item'    => array('inline', 'inline_empty', 'list'),
        'block_empty'  => array(),
        'inline_empty' => array(),
    );

    /**
     * The current group
     *
     * @var string
     */
    protected $_group = 'block';

    /**
     * Default attributes
     *
     * @var array
     */
    protected static $_defaultAttributes = array(
        'id'    => '',
        'class' => '',
        'style' => '',
        'lang'  => '',
        'title' => ''
    );


    /**
     * Execute a replace token
     *
     * @param  Zend_Markup_Token $token
     * @param  array $tag
     * @return string
     */
    protected function _executeReplace(Zend_Markup_Token $token, $tag)
    {
        if (isset($tag['tag'])) {
            if (!isset($tag['attributes'])) {
                $tag['attributes'] = array();
            }
            $attrs = self::_renderAttributes($token, $tag['attributes']);
            return "<{$tag['tag']}{$attrs}>{$this->_render($token)}</{$tag['tag']}>";
        }

        return parent::_executeReplace($token, $tag);
    }

    /**
     * Execute a single replace token
     *
     * @param  Zend_Markup_Token $token
     * @param  array $tag
     * @return string
     */
    protected function _executeSingleReplace(Zend_Markup_Token $token, $tag)
    {
        if (isset($tag['tag'])) {
            if (!isset($tag['attributes'])) {
                $tag['attributes'] = array();
            }
            $attrs = self::_renderAttributes($token, $tag['attributes']);
            return "<{$tag['tag']}{$attrs} />";
        }
        return parent::_executeSingleReplace($token, $tag);
    }

    /**
     * Render some attributes
     *
     * @param  Zend_Markup_Token $token
     * @param  array $tag
     * @return string
     */
    protected static function _renderAttributes(Zend_Markup_Token $token, array $attributes = array())
    {
        $attributes = array_merge(self::$_defaultAttributes, $attributes);

        $return = '';

        $tokenAttributes = $token->getAttributes();

        // correct style attribute
        if (isset($tokenAttributes['style'])) {
            $tokenAttributes['style'] = trim($tokenAttributes['style']);

            if ($tokenAttributes['style'][strlen($tokenAttributes['style']) - 1] != ';') {
                $tokenAttributes['style'] .= ';';
            }
        } else {
            $tokenAttributes['style'] = '';
        }

        // special treathment for 'align' and 'color' attribute
        if (isset($tokenAttributes['align'])) {
            $tokenAttributes['style'] .= 'text-align: ' . $tokenAttributes['align'] . ';';
            unset($tokenAttributes['align']);
        }
        if (isset($tokenAttributes['color']) && self::_checkColor($tokenAttributes['color'])) {
            $tokenAttributes['style'] .= 'color: ' . $tokenAttributes['color'] . ';';
            unset($tokenAttributes['color']);
        }

        /*
         * loop through all the available attributes, and check if there is
         * a value defined by the token
         * if there is no value defined by the token, use the default value or
         * don't set the attribute
         */
        foreach ($attributes as $attribute => $value) {
            if (isset($tokenAttributes[$attribute]) && !empty($tokenAttributes[$attribute])) {
                $return .= ' ' . $attribute . '="' . htmlentities($tokenAttributes[$attribute], ENT_QUOTES) . '"';
            } elseif (!empty($value)) {
                $return .= ' ' . $attribute . '="' . htmlentities($value, ENT_QUOTES) . '"';
            }
        }

        return $return;
    }

    /**
     * Method for the URL tag
     *
     * @param  Zend_Markup_Token $token
     * @param  string $text
     * @return string
     */
    protected static function _htmlUrl(Zend_Markup_Token $token, $text)
    {
        if ($token->hasAttribute('url')) {
            $url = $token->getAttribute('url');
        } else {
            $url = $text;
        }

        // check if the URL is valid
        if (!Zend_Uri::check($url)) {
            return $text;
        }

        $attributes = self::_renderAttributes($token);

        return "<a href=\"{$url}\"{$attributes}>{$text}</a>";
    }

    /**
     * Method for the img tag
     *
     * @param  Zend_Markup_Token $token
     * @param  string $text
     * @return string
     */
    protected static function _htmlImg(Zend_Markup_Token $token, $text)
    {
        $url = $text;

        // check if the URL is valid
        if (!Zend_Uri::check($url)) {
            return $text;
        }

        if ($token->hasAttribute('alt')) {
            $alt = $token->getAttribute('alt');
        } else {
            // try to get the alternative from the URL
            $alt = rtrim($text, '/');
            $alt = strrchr($alt, '/');
            if (false !== strpos($alt, '.')) {
                $alt = substr($alt, 1, strpos($alt, '.') - 1);
            }
        }

        return "<img src=\"{$url}\" alt=\"{$alt}\"" . self::_renderAttributes($token) . " />";
    }

    /**
     * Method for the list tag
     *
     * @param  Zend_Markup_Token $token
     * @param  string $text
     * @return void
     */
    protected static function _htmlList(Zend_Markup_Token $token, $text)
    {
        $type = null;
        if ($token->hasAttribute('list')) {
            // because '01' == '1'
            if ($token->getAttribute('list') === '01') {
                $type = 'decimal-leading-zero';
            } else {
                switch ($token->getAttribute('list')) {
                    case '1':
                        $type = 'decimal';
                        break;
                    case 'i':
                        $type = 'lower-roman';
                        break;
                    case 'I':
                        $type = 'upper-roman';
                        break;
                    case 'a':
                        $type = 'lower-alpha';
                        break;
                    case 'A':
                        $type = 'upper-alpha';
                        break;

                    // the following type is unsupported by IE (including IE8)
                    case 'alpha':
                        $type = 'lower-greek';
                        break;

                    // the CSS names itself
                    case 'armenian': // unsupported by IE (including IE8)
                    case 'decimal':
                    case 'decimal-leading-zero': // unsupported by IE (including IE8)
                    case 'georgian': // unsupported by IE (including IE8)
                    case 'lower-alpha':
                    case 'lower-greek': // unsupported by IE (including IE8)
                    case 'lower-latin': // unsupported by IE (including IE8)
                    case 'lower-roman':
                    case 'upper-alpha':
                    case 'upper-latin': // unsupported by IE (including IE8)
                    case 'upper-roman':
                        $type = $token->getAttribute('list');
                        break;
                }
            }
        }

        if (null !== $type) {
            return "<ol style=\"list-style-type: {$type}\">{$text}</ol>";
        } else {
            return "<ul>{$text}</ul>";
        }
    }

    /**
     * Method for the code tag
     *
     * @param  Zend_Markup_Token $token
     * @param  string $text
     * @return string
     */
    protected static function _htmlCode(Zend_Markup_Token $token, $text)
    {
        return highlight_string($text, true);
    }

    /**
     * Check if a color is a valid HTML color
     *
     * @param string $color
     *
     * @return bool
     */
    protected static function _checkColor($color)
    {
        /*
         * aqua, black, blue, fuchsia, gray, green, lime, maroon, navy, olive,
         * purple, red, silver, teal, white, and yellow.
         */
        $colors = array(
            'aqua', 'black', 'blue', 'fuchsia', 'gray', 'green', 'lime',
            'maroon', 'navy', 'olive', 'purple', 'red', 'silver', 'teal',
            'white', 'yellow'
        );

        if (in_array($color, $colors)) {
            return true;
        }

        if (preg_match('/\#[0-9a-f]{6}/i', $color)) {
            return true;
        }

        return false;
    }

    /**
     * Filter method, used for converting newlines to <br /> tags
     *
     * @param  string $value
     * @return string
     */
    protected function _filter($value)
    {
        if ($this->_filter) {
            return nl2br(htmlentities($value, ENT_QUOTES));
        }
        return $value;
    }
}
