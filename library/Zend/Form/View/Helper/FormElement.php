<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\View\Helper\AbstractHelper as BaseAbstractHelper;

class FormElement extends BaseAbstractHelper
{
    /**
     * Instance map to view helper
     *
     * @var array
     */
    protected $instanceMap = array(
        'Zend\Form\Element\Button' => 'form_button',
        'Zend\Form\Element\Captcha' => 'form_captcha',
        'Zend\Form\Element\Csrf' => 'form_hidden',
        'Zend\Form\Element\Collection' => 'form_collection',
        'Zend\Form\Element\DateTimeSelect' => 'form_date_time_select',
        'Zend\Form\Element\DateSelect' => 'form_date_select',
        'Zend\Form\Element\MonthSelect' => 'form_month_select',
    );

    /**
     * Type map to view helper
     *
     * @var array
     */
    protected $typeMap = array(
        'checkbox' => 'form_checkbox',
        'color' => 'form_color',
        'date' => 'form_date',
        'datetime' => 'form_date_time',
        'datetime-local' => 'form_date_time_local',
        'email' => 'form_email',
        'file' => 'form_file',
        'hidden' => 'form_hidden',
        'image' => 'form_image',
        'month' => 'form_month',
        'multi_checkbox' => 'form_multi_checkbox',
        'number' => 'form_number',
        'password' => 'form_password',
        'radio' => 'form_radio',
        'range' => 'form_range',
        'reset' => 'form_reset',
        'search' => 'form_search',
        'select' => 'form_select',
        'submit' => 'form_submit',
        'tel' => 'form_tel',
        'text' => 'form_text',
        'textarea' => 'form_textarea',
        'time' => 'form_time',
        'url' => 'form_url',
        'week' => 'form_week',
    );

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

        foreach ($this->instanceMap as $class => $pluginName) {
            if (is_a($element, $class)) {
                $helper = $renderer->plugin($pluginName);
                return $helper($element);
            }
        }

        $type = $element->getAttribute('type');

        foreach ($this->typeMap as $typeName => $pluginName) {
            if ($typeName == $type) {
                $helper = $renderer->plugin($pluginName);
                return $helper($element);
            }
        }

        $helper = $renderer->plugin('form_input');
        return $helper($element);
    }

    /**
     * Add type map to plugin
     *
     * @param string $type
     * @param string $plugin
     * @return self
     */
    public function addType($type, $plugin)
    {
        $this->typeMap[$type] = $plugin;

        return $this;
    }

    /**
     * Add instance map to plugin
     *
     * @param string $instance
     * @param string $plugin
     * @return self
     */
    public function addInstance($instance, $plugin)
    {
        $this->instanceMap[$instance] = $plugin;

        return $this;
    }
}
