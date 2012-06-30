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
use Zend\Form\FieldsetInterface;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FormCollection extends AbstractHelper
{
    /**
     * If set to true, collections are automatically wrapped around a fieldset
     *
     * @var boolean
     */
    protected $shouldWrap = true;

    /**
     * @var FormRow
     */
    protected $rowHelper;


    /**
     * Render a collection by iterating through all fieldsets and elements
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

        $markup = '';
        $templateMarkup = '';
        $attributes = $element->getAttributes();
        $escapeHelper = $this->getEscapeHelper();
        $rowHelper = $this->getRowHelper();

        foreach($element->getIterator() as $elementOrFieldset) {
            if ($elementOrFieldset instanceof FieldsetInterface) {
                if ($elementOrFieldset->getAttribute('template') === true) {
                    $templateMarkup .= $this->render($elementOrFieldset);
                } else {
                    $markup .= $this->render($elementOrFieldset);
                }
            } elseif ($elementOrFieldset instanceof ElementInterface) {
                if ($elementOrFieldset->getAttribute('template') === true) {
                    $templateMarkup .= $rowHelper($elementOrFieldset);
                } else {
                    $markup .= $rowHelper($elementOrFieldset);
                }
            }
        }

        if (!empty($templateMarkup)) {
            $markup .= sprintf(
                '<div data-template="%s"></div>',
                $escapeHelper($templateMarkup)
            );
        }

        // Every collection is wrapped by a fieldset if needed
        if ($this->shouldWrap) {
            if (isset($attributes['label'])) {
                $label = $escapeHelper($attributes['label']);

                $markup = sprintf(
                    '<fieldset><legend>%s</legend>%s</fieldset>',
                    $label,
                    $markup
                );
            }
        }

        return $markup;
    }

    /**
     * Invoke helper as function
     *
     * Proxies to {@link render()}.
     *
     * @param  ElementInterface|null $element
     * @param  boolean $wrap
     * @return string
     */
    public function __invoke(ElementInterface $element = null, $wrap = true)
    {
        if (!$element) {
            return $this;
        }
        $this->setShouldWrap($wrap);

        return $this->render($element);
    }

    /**
     * If set to true, collections are automatically wrapped around a fieldset
     *
     * @param $wrap
     * @return FormCollection
     */
    public function setShouldWrap($wrap)
    {
        $this->shouldWrap = $wrap;
        return $this;
    }

    /**
     * Get wrapped
     *
     * @return bool
     */
    public function shouldWrap()
    {
        return $this->shouldWrap();
    }

    /**
     * Retrieve the FormRow helper
     *
     * @return FormRow
     */
    protected function getRowHelper()
    {
        if ($this->rowHelper) {
            return $this->rowHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->rowHelper = $this->view->plugin('form_row');
        }

        if (!$this->rowHelper instanceof FormRow) {
            $this->rowHelper = new FormRow();
        }

        return $this->rowHelper;
    }
}