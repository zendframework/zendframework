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
use Zend\Form\Exception;
use Zend\Form\View\Helper\AbstractHelper;

class FormRow extends AbstractHelper
{
    const LABEL_APPEND  = 'append';
    const LABEL_PREPEND = 'prepend';

    /**
     * @var string
     */
    protected $labelPosition = self::LABEL_PREPEND;

    /**
     * @var bool
     */
    protected $renderErrors = true;

    /**
     * @var array
     */
    protected $labelAttributes;

    /**
     * @var string
     */
    protected $inputErrorClass = 'input-error';

    /**
     * @var FormLabel
     */
    protected $labelHelper;

    /**
     * @var FormElement
     */
    protected $elementHelper;

    /**
     * @var FormElementErrors
     */
    protected $elementErrorsHelper;


    /**
     * Utility form helper that renders a label (if it exists), an element and errors
     *
     * @param ElementInterface $element
     * @return string
     * @throws \Zend\Form\Exception\DomainException
     */
    public function render(ElementInterface $element)
    {
        $escapeHtmlHelper    = $this->getEscapeHtmlHelper();
        $labelHelper         = $this->getLabelHelper();
        $elementHelper       = $this->getElementHelper();
        $elementErrorsHelper = $this->getElementErrorsHelper();

        $label           = $element->getLabel();
        $inputErrorClass = $this->getInputErrorClass();
        $elementErrors   = $elementErrorsHelper->render($element);

        // Does this element have errors ?
        if (!empty($elementErrors) && !empty($inputErrorClass)) {
            $classAttributes = ($element->hasAttribute('class') ? $element->getAttribute('class') . ' ' : '');
            $classAttributes = $classAttributes . $inputErrorClass;

            $element->setAttribute('class', $classAttributes);
        }

        $elementString = $elementHelper->render($element);

        if (isset($label) && '' !== $label) {
            // Translate the label
            if (null !== ($translator = $this->getTranslator())) {
                $label = $translator->translate(
                    $label, $this->getTranslatorTextDomain()
                );
            }

            $label = $escapeHtmlHelper($label);
            $labelAttributes = $element->getLabelAttributes();

            if (empty($labelAttributes)) {
                $labelAttributes = $this->labelAttributes;
            }

            // Multicheckbox elements have to be handled differently as the HTML standard does not allow nested
            // labels. The semantic way is to group them inside a fieldset
            $type = $element->getAttribute('type');
            if ($type === 'multi_checkbox' || $type === 'radio') {
                $markup = sprintf(
                    '<fieldset><legend>%s</legend>%s</fieldset>',
                    $label,
                    $elementString);
            } else {
                if ($element->hasAttribute('id')) {
                    $labelOpen = '';
                    $labelClose = '';
                    $label = $labelHelper($element);
                } else {
                    $labelOpen  = $labelHelper->openTag($labelAttributes);
                    $labelClose = $labelHelper->closeTag();
                }

                if ($label !== '' && !$element->hasAttribute('id')) {
                    $label = '<span>' . $label . '</span>';
                }

                switch ($this->labelPosition) {
                    case self::LABEL_PREPEND:
                        $markup = $labelOpen . $label . $elementString . $labelClose;
                        break;
                    case self::LABEL_APPEND:
                    default:
                        $markup = $labelOpen . $elementString . $label . $labelClose;
                        break;
                }
            }

            if ($this->renderErrors) {
                $markup .= $elementErrors;
            }
        } else {
            if ($this->renderErrors) {
                $markup = $elementString . $elementErrors;
            } else {
                $markup = $elementString;
            }
        }

        return $markup;
    }

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @param null|ElementInterface $element
     * @param null|string           $labelPosition
     * @param bool                  $renderErrors
     * @return string|FormRow
     */
    public function __invoke(ElementInterface $element = null, $labelPosition = null, $renderErrors = null)
    {
        if (!$element) {
            return $this;
        }

        if ($labelPosition !== null) {
            $this->setLabelPosition($labelPosition);
        }

        if ($renderErrors !== null){
            $this->setRenderErrors($renderErrors);
        }

        return $this->render($element);
    }

    /**
     * Set the label position
     *
     * @param $labelPosition
     * @return FormRow
     * @throws \Zend\Form\Exception\InvalidArgumentException
     */
    public function setLabelPosition($labelPosition)
    {
        $labelPosition = strtolower($labelPosition);
        if (!in_array($labelPosition, array(self::LABEL_APPEND, self::LABEL_PREPEND))) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects either %s::LABEL_APPEND or %s::LABEL_PREPEND; received "%s"',
                __METHOD__,
                __CLASS__,
                __CLASS__,
                (string) $labelPosition
            ));
        }
        $this->labelPosition = $labelPosition;

        return $this;
    }

    /**
     * Get the label position
     *
     * @return string
     */
    public function getLabelPosition()
    {
        return $this->labelPosition;
    }

    /**
     * Are the errors rendered by this helper ?
     *
     * @param  bool $renderErrors
     * @return FormRow
     */
    public function setRenderErrors($renderErrors)
    {
        $this->renderErrors = (bool) $renderErrors;
        return $this;
    }

    /**
     * @return bool
     */
    public function getRenderErrors()
    {
        return $this->renderErrors;
    }

    /**
     * Set the attributes for the row label
     *
     * @param  array $labelAttributes
     * @return FormRow
     */
    public function setLabelAttributes($labelAttributes)
    {
        $this->labelAttributes = $labelAttributes;
        return $this;
    }

    /**
     * Get the attributes for the row label
     *
     * @return array
     */
    public function getLabelAttributes()
    {
        return $this->labelAttributes;
    }

    /**
     * Set the class that is added to element that have errors
     *
     * @param  string $inputErrorClass
     * @return FormRow
     */
    public function setInputErrorClass($inputErrorClass)
    {
        $this->inputErrorClass = $inputErrorClass;
        return $this;
    }

    /**
     * Get the class that is added to element that have errors
     *
     * @return string
     */
    public function getInputErrorClass()
    {
        return $this->inputErrorClass;
    }

    /**
     * Retrieve the FormLabel helper
     *
     * @return FormLabel
     */
    protected function getLabelHelper()
    {
        if ($this->labelHelper) {
            return $this->labelHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->labelHelper = $this->view->plugin('form_label');
        }

        if (!$this->labelHelper instanceof FormLabel) {
            $this->labelHelper = new FormLabel();
        }

        if ($this->hasTranslator()) {
            $this->labelHelper->setTranslator(
                $this->getTranslator(),
                $this->getTranslatorTextDomain()
            );
        }

        return $this->labelHelper;
    }

    /**
     * Retrieve the FormElement helper
     *
     * @return FormElement
     */
    protected function getElementHelper()
    {
        if ($this->elementHelper) {
            return $this->elementHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->elementHelper = $this->view->plugin('form_element');
        }

        if (!$this->elementHelper instanceof FormElement) {
            $this->elementHelper = new FormElement();
        }

        return $this->elementHelper;
    }

    /**
     * Retrieve the FormElementErrors helper
     *
     * @return FormElementErrors
     */
    protected function getElementErrorsHelper()
    {
        if ($this->elementErrorsHelper) {
            return $this->elementErrorsHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->elementErrorsHelper = $this->view->plugin('form_element_errors');
        }

        if (!$this->elementErrorsHelper instanceof FormElementErrors) {
            $this->elementErrorsHelper = new FormElementErrors();
        }

        return $this->elementErrorsHelper;
    }
}
