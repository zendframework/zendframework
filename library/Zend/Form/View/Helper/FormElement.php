<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace Zend\Form\View\Helper;

use Zend\Form\Element;
use Zend\Form\ElementInterface;
use Zend\View\Helper\AbstractHelper as BaseAbstractHelper;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 */
class FormElement extends BaseAbstractHelper
{
    /**
     * Render an element
     *
     * Introspects the element type and attributes to determine which
     * helper to utilize when rendering.
     *
     * @param  ElementInterface $element
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin')) {
            // Bail early if renderer is not pluggable
            return '';
        }

        if ($element instanceof Element\Captcha) {
            $helper = $renderer->plugin('form_captcha');
            return $helper($element);
        }

        if ($element instanceof Element\Csrf) {
            $helper = $renderer->plugin('form_hidden');
            return $helper($element);
        }

        if ($element instanceof Element\Collection) {
            $helper = $renderer->plugin('form_collection');
            return $helper($element);
        }

        $type    = $element->getAttribute('type');
        $options = $element->getAttribute('options');

        if ('checkbox' == $type) {
            $helper = $renderer->plugin('form_checkbox');
            return $helper($element);
        }

        if ('color' == $type) {
            $helper = $renderer->plugin('form_color');
            return $helper($element);
        }

        if ('multi_checkbox' == $type && is_array($options)) {
            $helper = $renderer->plugin('form_multi_checkbox');
            return $helper($element);
        }

        if ('radio' == $type && is_array($options)) {
            $helper = $renderer->plugin('form_radio');
            return $helper($element);
        }

        if ('select' == $type && is_array($options)) {
            $helper = $renderer->plugin('form_select');
            return $helper($element);
        }

        if ('textarea' == $type) {
            $helper = $renderer->plugin('form_textarea');
            return $helper($element);
        }

        $helper = $renderer->plugin('form_input');
        return $helper($element);
    }

    /**
     * Invoke helper as function
     *
     * Proxies to {@link render()}.
     *
     * @param  ElementInterface|null $element
     * @return string|FormElement
     */
    public function __invoke(ElementInterface $element = null)
    {
        if (!$element) {
            return $this;
        }

        return $this->render($element);
    }
}
