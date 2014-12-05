<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Helper;

/**
 * Renders <html> tag (both opening and closing) of a web page, to which some custom
 * attributes can be added dynamically.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class HtmlTag extends AbstractHtmlElement
{
    /**
     * Attributes for the <html> tag.
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * Whether to add appropriate attributes in accordance with currently set DOCTYPE.
     *
     * @var bool
     */
    protected $addDoctypeAttributes = false;

    /**
     * @var bool
     */
    private $doctypeAttribsAdded = false;

    /**
     * Retrieve object instance; optionally add attributes.
     *
     * @param array $attribs
     * @return self
     */
    public function __invoke(array $attribs = array())
    {
        if (!empty($attribs)) {
            $this->setAttributes($attribs);
        }

        return $this;
    }

    /**
     * Set new attribute.
     *
     * @param string $attrName
     * @param string $attrValue
     * @return self
     */
    public function setAttribute($attrName, $attrValue)
    {
        $this->attributes[$attrName] = $attrValue;
        return $this;
    }

    /**
     * Add new or overwrite the existing attributes.
     *
     * @param array $attribs
     * @return self
     */
    public function setAttributes(array $attribs)
    {
        foreach ($attribs as $name => $value) {
            $this->setAttribute($name, $value);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param bool $addDoctypeAttributes
     * @return self
     */
    public function setAddDoctypeAttributes($addDoctypeAttributes)
    {
        $this->addDoctypeAttributes = (bool) $addDoctypeAttributes;
        return $this;
    }

    /**
     * @return bool
     */
    public function getAddDoctypeAttributes()
    {
        return $this->addDoctypeAttributes;
    }

    /**
     * Render opening tag.
     *
     * @return string
     */
    public function openTag()
    {
        $this->handleDoctypeAttributes();
        
        return sprintf('<html%s>', $this->htmlAttribs($this->attributes));
    }

    protected function handleDoctypeAttributes()
    {
        if ($this->addDoctypeAttributes && !$this->doctypeAttribsAdded) {
            if (method_exists($this->view, 'plugin')) {
                $doctypeAttributes = array();

                if ($this->view->plugin('doctype')->isXhtml()) {
                    $doctypeAttributes = array('xmlns' => 'http://www.w3.org/1999/xhtml');
                }

                if (!empty($doctypeAttributes)) {
                    $this->attributes = array_merge($doctypeAttributes, $this->attributes);
                }
            }

            $this->doctypeAttribsAdded = true;
        }
    }

    /**
     * Render closing tag.
     *
     * @return string
     */
    public function closeTag()
    {
        return '</html>';
    }
}
