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

use Zend\Form\Element;
use Zend\Form\ElementInterface;
use Zend\Loader\Pluggable;
use Zend\View\Helper\AbstractHelper as BaseAbstractHelper;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
        if (!$renderer instanceof Pluggable) {
            // Bail early if renderer is not pluggable
            return '';
        }

        if ($element instanceof Element\Captcha) {
            $helper = $renderer->plugin('form_captcha');
            return $helper($element);
        }

        if ($element instanceof Element\Csrf) {
            $helper = $renderer->plugin('form_input');
            return $helper($element);
        }

        $type    = $element->getAttribute('type');
        $options = $element->getAttribute('options');
        $captcha = $element->getAttribute('captcha');

        if (!empty($captcha)) {
            $helper = $renderer->plugin('form_captcha');
            return $helper($element);
        }

        if (is_array($options) && $type == 'radio') {
            $helper = $renderer->plugin('form_radio');
            return $helper($element);
        }

        if (is_array($options) && $type == 'checkbox') {
            $helper = $renderer->plugin('form_multi_checkbox');
            return $helper($element);
        }

        if (is_array($options) && $type == 'checkbox') {
            $helper = $renderer->plugin('form_multi_checkbox');
            return $helper($element);
        }

        if (is_array($options) && $type == 'select') {
            $helper = $renderer->plugin('form_select');
            return $helper($element);
        }

        if ($type == 'textarea') {
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
     * @param  ElementInterface $element 
     * @return string
     */
    public function __invoke(ElementInterface $element)
    {
        return $this->render($element);
    }
}
