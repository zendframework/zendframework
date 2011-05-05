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
 * @subpackage Renderer_Markup_HTML
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Markup\Renderer\Markup\Html;

use Zend\Markup\Renderer\Markup\AbstractMarkup,
    Zend\Markup,
    Zend\Filter\HtmlEntities as HtmlEntitiesFilter,
    Zend\Filter\Callback as CallbackFilter;

/**
 * Abstract markup
 *
 * @uses       \Zend\Markup\Renderer\Markup\AbstractMarkup
 * @uses       \Zend\Markup\Renderer\AbstractRenderer
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer_Markup_Html
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractHtml extends AbstractMarkup
{

    /**
     * Constructor, adds default filters for the filter chain
     *
     * @return void
     */
    public function __construct()
    {
        $this->addFilter(new HtmlEntitiesFilter(array(
            'encoding'   => $this->getEncoding(),
            'quotestyle' => ENT_QUOTES
        )));
        $this->addFilter(new CallbackFilter('nl2br'));
    }

    /**
     * Attributes for this markup
     *
     * @var array
     */
    protected $_attributes = array();


    /**
     * Set the attributes for this markup
     *
     * @param array $attributes
     *
     * @return \Zend\Markup\Renderer\Markup\Html\AbstractHtml
     */
    public function setAttributes(array $attributes)
    {
        $this->_attributes = $attributes;

        return $this;
    }

    /**
     * Add an attribute for this markup
     *
     * @param string $name
     * @param string $value
     *
     * @return \Zend\Markup\Renderer\Markup\Html\AbstractHtml
     */
    public function addAttribute($name, $value)
    {
        $this->_attributes[$name] = $value;

        return $this;
    }

    /**
     * Remove an attribute from this markup
     *
     * @param string $name
     *
     * @return \Zend\Markup\Renderer\Markup\Html\AbstractHtml
     */
    public function removeAttribute($name)
    {
        unset($this->_attributes[$name]);

        return $this;
    }

    /**
     * Render some attributes
     *
     * @param  \Zend\Markup\Token $token
     * @return string
     */
    public function renderAttributes(Markup\Token $token)
    {
        $return = '';

        $tokenAttributes = $token->getAttributes();

        /*
         * loop through all the available attributes, and check if there is
         * a value defined by the token
         * if there is no value defined by the token, use the default value or
         * don't set the attribute
         */
        foreach ($this->_attributes as $attribute => $value) {
            if (isset($tokenAttributes[$attribute]) && !empty($tokenAttributes[$attribute])) {
                $return .= ' ' . $attribute . '="' . htmlentities($tokenAttributes[$attribute],
                                                                  ENT_QUOTES,
                                                                  $this->getEncoding()) . '"';
            } elseif (!empty($value)) {
                $return .= ' ' . $attribute . '="' . htmlentities($value, ENT_QUOTES, $this->getEncoding()) . '"';
            }
        }

        return $return;
    }
}
