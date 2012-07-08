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
use Zend\Form\Exception;
use Zend\Form\View\Helper\AbstractHelper;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FormRow extends AbstractHelper
{
    const LABEL_APPEND  = 'append';
    const LABEL_PREPEND = 'prepend';

    /**
     * @var string
     */
    protected $labelPosition = self::LABEL_PREPEND;

    /**
     * @var array
     */
    protected $labelAttributes;

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
        $label               = $element->getLabel();
        $elementString       = $elementHelper->render($element);
        $elementErrors       = $elementErrorsHelper->render($element);

        if (!empty($label)) {
            $label = $escapeHtmlHelper($label);
            $labelAttributes = $element->getLabelAttributes();

            if (empty($labelAttributes)) {
                $labelAttributes = $this->labelAttributes;
            }

            // Multicheckbox elements have to be handled differently as the HTML standard does not allow nested
            // labels. The semantic way is to group them inside a fieldset
            $type = $element->getAttribute('type');
            if ($type === 'multi_checkbox' || $type === 'multicheckbox' || $type === 'radio') {
                $markup = sprintf(
                    '<fieldset><legend>%s</legend>%s</fieldset>',
                    $label,
                    $elementString);
            } else {
                $labelOpen  = $labelHelper->openTag($labelAttributes);
                $labelClose = $labelHelper->closeTag();

                switch ($this->labelPosition) {
                    case self::LABEL_PREPEND:
                        $markup = $labelOpen . $label . $elementString . $labelClose . $elementErrors;
                        break;
                    case self::LABEL_APPEND:
                    default:
                        $markup = $labelOpen . $elementString . $label . $labelClose . $elementErrors;
                        break;
                }
            }
        } else {
            $markup = $elementString . $elementErrors;
        }

        return $markup;
    }

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @param null|ElementInterface $element
     * @param null|string $labelPosition
     * @return string|FormRow
     */
    public function __invoke(ElementInterface $element = null, $labelPosition = null)
    {
        if (!$element) {
            return $this;
        }

        if ($labelPosition !== null) {
            $this->setLabelPosition($labelPosition);
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
