<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Markup
 */

namespace Zend\Markup\Renderer\Markup\Html;

use Zend\Filter\Callback as CallbackFilter;
use Zend\Filter\HtmlEntities as HtmlEntitiesFilter;
use Zend\Markup;
use Zend\Markup\Renderer\Markup\AbstractMarkup;

/**
 * Abstract markup
 *
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer_Markup_Html
 */
abstract class AbstractHtml extends AbstractMarkup
{

    /**
     * Constructor, adds default filters for the filter chain
     *
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
