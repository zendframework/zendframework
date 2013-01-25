<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace Zend\Form\View\Helper;

use RuntimeException;
use Zend\Form\Element;
use Zend\Form\ElementInterface;
use Zend\Form\Element\Collection as CollectionElement;
use Zend\Form\FieldsetInterface;
use Zend\View\Helper\AbstractHelper as BaseAbstractHelper;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 */
class FormCollection extends AbstractHelper
{
    /**
     * If set to true, collections are automatically wrapped around a fieldset
     *
     * @var bool
     */
    protected $shouldWrap = true;

    /**
     * The name of the default view helper that is used to render sub elements.
     *
     * @var string
     */
    protected $defaultElementHelper = 'formrow';

    /**
     * The view helper used to render sub elements.
     *
     * @var AbstractHelper
     */
    protected $elementHelper;

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
        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        $elementHelper = $this->getElementHelper();

        if ($element instanceof CollectionElement && $element->shouldCreateTemplate()) {
            $elementOrFieldset = $element->getTemplateElement();

            if ($elementOrFieldset instanceof FieldsetInterface) {
                $templateMarkup .= $this->render($elementOrFieldset);
            } elseif ($elementOrFieldset instanceof ElementInterface) {
                $templateMarkup .= $elementHelper($elementOrFieldset);
            }
        }

        foreach ($element->getIterator() as $elementOrFieldset) {
            if ($elementOrFieldset instanceof FieldsetInterface) {
                $markup .= $this->render($elementOrFieldset);
            } elseif ($elementOrFieldset instanceof ElementInterface) {
                $markup .= $elementHelper($elementOrFieldset);
            }
        }

        // If $templateMarkup is not empty, use it for simplify adding new element in JavaScript
        if (!empty($templateMarkup)) {
            $escapeHtmlAttribHelper = $this->getEscapeHtmlAttrHelper();

            $markup .= sprintf(
                '<span data-template="%s"></span>',
                $escapeHtmlAttribHelper($templateMarkup)
            );
        }

        // Every collection is wrapped by a fieldset if needed
        if ($this->shouldWrap) {
            $label = $element->getLabel();

            if (!empty($label)) {
                $label = $escapeHtmlHelper($label);

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
     * @param  bool $wrap
     * @return string|FormCollection
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
     * @param bool $wrap
     * @return FormCollection
     */
    public function setShouldWrap($wrap)
    {
        $this->shouldWrap = (bool) $wrap;
        return $this;
    }

    /**
     * Get wrapped
     *
     * @return bool
     */
    public function shouldWrap()
    {
        return $this->shouldWrap;
    }

    /**
     * Gets the name of the view helper that should be used to render sub elements.
     *
     * @return string
     */
    public function getDefaultElementHelper()
    {
        return $this->defaultElementHelper;
    }

    /**
     * Sets the name of the view helper that should be used to render sub elements.
     *
     * @param string $defaultSubHelper The name of the view helper to set.
     * @return FormCollection
     */
    public function setDefaultElementHelper($defaultSubHelper)
    {
        $this->defaultElementHelper = $defaultSubHelper;
        return $this;
    }

    /**
     * Retrieve the element helper.
     *
     * @throws RuntimeException
     * @return AbstractHelper
     */
    protected function getElementHelper()
    {
        if ($this->elementHelper) {
            return $this->elementHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->elementHelper = $this->view->plugin($this->getDefaultElementHelper());
        }

        if (!$this->elementHelper instanceof BaseAbstractHelper) {
            // @todo Ideally the helper should implement an interface.
            throw new RuntimeException('Invalid element helper set in FormCollection. The helper must be an instance of AbstractHelper.');
        }

        return $this->elementHelper;
    }

    /**
     * Sets the element helper that should be used by this collection.
     *
     * @param AbstractHelper $elementHelper The element helper to use.
     * @return FormCollection
     */
    public function setElementHelper(AbstractHelper $elementHelper)
    {
        $this->elementHelper = $elementHelper;
        return $this;
    }
}
