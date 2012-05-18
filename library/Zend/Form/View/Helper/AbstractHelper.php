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
 * @package    Zend_Form
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Loader\Pluggable;
use Zend\View\Helper\AbstractHelper as BaseAbstractHelper;
use Zend\View\Helper\Doctype;
use Zend\View\Helper\Escape;

/**
 * Base functionality for all form view helpers
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractHelper extends BaseAbstractHelper
{
    /**
     * Standard boolean attributes, with expected values for enabling/disabling
     * 
     * @var array
     */
    protected $booleanAttributes = array(
        'autocomplete' => array('on' => 'on',        'off' => 'off'),
        'autofocus'    => array('on' => 'autofocus', 'off' => ''),
        'checked'      => array('on' => 'checked',   'off' => ''),
        'disabled'     => array('on' => 'disabled',  'off' => ''),
        'multiple'     => array('on' => 'multiple',  'off' => ''),
        'readonly'     => array('on' => 'readonly',  'off' => ''),
        'required'     => array('on' => 'required',  'off' => ''),
        'selected'     => array('on' => 'selected',  'off' => ''),
    );

    /**
     * @var Doctype
     */
    protected $doctypeHelper;

    /**
     * @var Escape
     */
    protected $escapeHelper;

    /**
     * Attributes globally valid for all tags
     * 
     * @var array
     */
    protected $validGlobalAttributes = array(
        'accesskey'          => true,
        'class'              => true,
        'contenteditable'    => true,
        'contextmenu'        => true,
        'dir'                => true,
        'draggable'          => true,
        'dropzone'           => true,
        'hidden'             => true,
        'id'                 => true,
        'lang'               => true,
        'onabort'            => true,
        'onblur'             => true,
        'oncanplay'          => true,
        'oncanplaythrough'   => true,
        'onchange'           => true,
        'onclick'            => true,
        'oncontextmenu'      => true,
        'ondblclick'         => true,
        'ondrag'             => true,
        'ondragend'          => true,
        'ondragenter'        => true,
        'ondragleave'        => true,
        'ondragover'         => true,
        'ondragstart'        => true,
        'ondrop'             => true,
        'ondurationchange'   => true,
        'onemptied'          => true,
        'onended'            => true,
        'onerror'            => true,
        'onfocus'            => true,
        'oninput'            => true,
        'oninvalid'          => true,
        'onkeydown'          => true,
        'onkeypress'         => true,
        'onkeyup'            => true,
        'onload'             => true,
        'onloadeddata'       => true,
        'onloadedmetadata'   => true,
        'onloadstart'        => true,
        'onmousedown'        => true,
        'onmousemove'        => true,
        'onmouseout'         => true,
        'onmouseover'        => true,
        'onmouseup'          => true,
        'onmousewheel'       => true,
        'onpause'            => true,
        'onplay'             => true,
        'onplaying'          => true,
        'onprogress'         => true,
        'onratechange'       => true,
        'onreadystatechange' => true,
        'onreset'            => true,
        'onscroll'           => true,
        'onseeked'           => true,
        'onseeking'          => true,
        'onselect'           => true,
        'onshow'             => true,
        'onstalled'          => true,
        'onsubmit'           => true,
        'onsuspend'          => true,
        'ontimeupdate'       => true,
        'onvolumechange'     => true,
        'onwaiting'          => true,
        'spellcheck'         => true,
        'style'              => true,
        'tabindex'           => true,
        'title'              => true,
        'xml:base'           => true,
        'xml:lang'           => true,
        'xml:space'          => true,
    );

    /**
     * Attributes valid for the tag represented by this helper
     *
     * This should be overridden in extending classes
     * 
     * @var array
     */
    protected $validTagAttributes = array(
    );

    /**
     * Set value for doctype
     *
     * @param  string $doctype
     * @return AbstractHelper
     */
    public function setDoctype($doctype)
    {
        $this->getDoctypeHelper()->setDoctype($doctype);
        return $this;
    }
    
    /**
     * Get value for doctype
     *
     * @return string
     */
    public function getDoctype()
    {
        return $this->getDoctypeHelper()->getDoctype();
    }

    /**
     * Set value for character encoding
     *
     * @param  string encoding
     * @return AbstractHelper
     */
    public function setEncoding($encoding)
    {
        $this->getEscapeHelper()->setEncoding($encoding);
        return $this;
    }
    
    /**
     * Get character encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->getEscapeHelper()->getEncoding();
    }

    /**
     * Create a string of all attribute/value pairs
     *
     * Escapes all attribute values
     * 
     * @param  array $attributes 
     * @return string
     */
    public function createAttributesString(array $attributes)
    {
        $attributes = $this->prepareAttributes($attributes);

        $escape  = $this->getEscapeHelper();
        $strings = array();
        foreach ($attributes as $key => $value) {
            $key = strtolower($key);
            if (!$value && isset($this->booleanAttributes[$key])) {
                // Skip boolean attributes that expect empty string as false value
                if ('' === $this->booleanAttributes[$key]['off']) {
                    continue;
                }
            }
            $strings[] = sprintf('%s="%s"', $key, $escape($value));
        }
        return implode(' ', $strings);
    }

    /**
     * Get the ID of an element
     *
     * If no ID attribute present, attempts to use the name attribute.
     * If no name attribute is present, either, returns null.
     * 
     * @param  ElementInterface $element 
     * @return null|string
     */
    public function getId(ElementInterface $element)
    {
        $id = $element->getAttribute('id');
        if (null !== $id) {
            return $id;
        }

        return $element->getName();
    }

    /**
     * Get the closing bracket for an inline tag
     *
     * Closes as either "/>" for XHTML doctypes or ">" otherwise.
     * 
     * @return string
     */
    public function getInlineClosingBracket()
    {
        $doctypeHelper = $this->getDoctypeHelper();
        if ($doctypeHelper->isXhtml()) {
            return '/>';
        }
        return '>';
    }

    /**
     * Retrieve the doctype helper
     * 
     * @return Doctype
     */
    protected function getDoctypeHelper()
    {
        if ($this->doctypeHelper) {
            return $this->doctypeHelper;
        }

        if ($this->view instanceof Pluggable) {
            $this->doctypeHelper = $this->view->plugin('doctype');
        }

        if (!$this->doctypeHelper instanceof Doctype) {
            $this->doctypeHelper = new Doctype();
        }

        return $this->doctypeHelper;
    }

    /**
     * Retrieve the escape helper
     * 
     * @return Escape
     */
    protected function getEscapeHelper()
    {
        if ($this->escapeHelper) {
            return $this->escapeHelper;
        }

        if ($this->view instanceof Pluggable) {
            $this->escapeHelper = $this->view->plugin('escape');
        }

        if (!$this->escapeHelper instanceof Escape) {
            $this->escapeHelper = new Escape();
        }

        return $this->escapeHelper;
    }

    /**
     * Prepare attributes for rendering
     *
     * Ensures appropriate attributes are present (e.g., if "name" is present, 
     * but no "id", sets the latter to the former).
     *
     * Removes any invalid attributes
     * 
     * @param  array $attributes 
     * @return array
     */
    protected function prepareAttributes(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $attribute = strtolower($key);

            if (!isset($this->validGlobalAttributes[$attribute])
                && !isset($this->validTagAttributes[$attribute])
                && 'data-' != substr($attribute, 0, 5)
            ) {
                // Invalid attribute for the current tag
                unset($attributes[$key]);
                continue;
            }

            // Normalize attribute key, if needed
            if ($attribute != $key) {
                unset($attributes[$key]);
                $attributes[$attribute] = $value;
            }

            // Normalize boolean attribute values
            if (isset($this->booleanAttributes[$attribute])) {
                $attributes[$attribute] = $this->prepareBooleanAttributeValue($attribute, $value);
            }
        }

        return $attributes;
    }

    /**
     * Prepare a boolean attribute value
     *
     * Prepares the expected representation for the boolean attribute specified.
     * 
     * @param  string $attribute 
     * @param  mixed $value 
     * @return string
     */
    protected function prepareBooleanAttributeValue($attribute, $value)
    {
        $value = (bool) $value;
        return ($value 
            ? $this->booleanAttributes[$attribute]['on']
            : $this->booleanAttributes[$attribute]['off']
        );
    }
}
