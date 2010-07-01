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
 */

/**
 * @namespace
 */
namespace Zend\Markup\Renderer;

use Zend\Markup\AbstractRenderer,
    Zend\Markup\Token,
    Zend\Loader\PluginLoader;

/**
 * HTML renderer
 *
 * @uses       \Zend\Filter\FilterChain
 * @uses       \Zend\Filter\Callback
 * @uses       \Zend\Filter\HtmlEntities
 * @uses       \Zend\Filter\PregReplace
 * @uses       \Zend\Loader\PluginLoader
 * @uses       \Zend\Markup\AbstractRenderer
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class HTML extends AbstractRenderer
{

    /**
     * Element groups
     *
     * @var array
     */
    protected $_groups = array(
        'block'        => array('block', 'inline', 'block-empty', 'inline-empty', 'list'),
        'inline'       => array('inline', 'inline-empty'),
        'list'         => array('list-item'),
        'list-item'    => array('inline', 'inline-empty', 'list'),
        'block-empty'  => array(),
        'inline-empty' => array(),
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
     * Constructor
     *
     * @param array|\Zend\Config\Config $options
     *
     * @return void
     */
    public function __construct($options = array())
    {
        if ($options instanceof \Zend\Config\Config) {
            $options = $options->toArray();
        }

        $this->_pluginLoader = new PluginLoader(array(
            'Zend\Markup\Renderer\HTML' => 'Zend/Markup/Renderer/HTML/'
        ));

        $this->_defineDefaultMarkups();

        parent::__construct($options);
    }

    /**
     * Define the default markups
     *
     * @return void
     */
    protected function _defineDefaultMarkups()
    {
    }

    /**
     * Add the default filters
     *
     * @return void
     */
    public function addDefaultFilters()
    {
        $this->_defaultFilter = new \Zend\Filter\FilterChain();

        $this->_defaultFilter->addFilter(new \Zend\Filter\HtmlEntities(array('encoding' => self::getEncoding())));
        $this->_defaultFilter->addFilter(new \Zend\Filter\Callback('nl2br'));
    }

    /**
     * Execute a replace token
     *
     * @param  \Zend\Markup\Token $token
     * @param  array $markup
     * @return string
     */
    protected function _executeReplace(Token $token, $markup)
    {
        if (isset($markup['tag'])) {
            if (!isset($markup['attributes'])) {
                $markup['attributes'] = array();
            }
            $attrs = self::renderAttributes($token, $markup['attributes']);
            return "<{$markup['tag']}{$attrs}>{$this->_render($token)}</{$markup['tag']}>";
        }

        return parent::_executeReplace($token, $markup);
    }

    /**
     * Execute a single replace token
     *
     * @param  \Zend\Markup\Token $token
     * @param  array $markup
     * @return string
     */
    protected function _executeSingleReplace(Token $token, $markup)
    {
        if (isset($markup['tag'])) {
            if (!isset($markup['attributes'])) {
                $markup['attributes'] = array();
            }
            $attrs = self::renderAttributes($token, $markup['attributes']);
            return "<{$markup['tag']}{$attrs} />";
        }
        return parent::_executeSingleReplace($token, $markup);
    }

    /**
     * Render some attributes
     *
     * @param  \Zend\Markup\Token $token
     * @param  array $attributes
     * @return string
     */
    public static function renderAttributes(Token $token, array $attributes = array())
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
        if (isset($tokenAttributes['color']) && self::checkColor($tokenAttributes['color'])) {
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
                $return .= ' ' . $attribute . '="' . htmlentities($tokenAttributes[$attribute],
                                                                  ENT_QUOTES,
                                                                  self::getEncoding()) . '"';
            } elseif (!empty($value)) {
                $return .= ' ' . $attribute . '="' . htmlentities($value, ENT_QUOTES, self::getEncoding()) . '"';
            }
        }

        return $return;
    }

    /**
     * Check if a color is a valid HTML color
     *
     * @param string $color
     *
     * @return bool
     */
    public static function checkColor($color)
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
     * Check if the URI is valid
     *
     * @param string $uri
     *
     * @return bool
     */
    public static function isValidUri($uri)
    {
        if (!preg_match('/^([a-z][a-z+\-.]*):/i', $uri, $matches)) {
            return false;
        }

        $scheme = strtolower($matches[1]);

        switch ($scheme) {
            case 'javascript':
                // JavaScript scheme is not allowed for security reason
                return false;

            case 'http':
            case 'https':
            case 'ftp':
                $components = @parse_url($uri);

                if ($components === false) {
                    return false;
                }

                if (!isset($components['host'])) {
                    return false;
                }

                return true;

            default:
                return true;
        }
    }
}
