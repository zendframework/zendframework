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
use Zend\Loader\Pluggable;
use Zend\View\Helper\AbstractHelper as BaseAbstractHelper;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FormRow extends BaseAbstractHelper
{
    const LABEL_APPEND  = 'append';
    const LABEL_PREPEND = 'prepend';

    /**
     * @var string
     */
    protected $labelPosition = self::LABEL_PREPEND;


    /**
     * Utility form helper that renders a label (if it exists), an element and errors
     *
     * @param ElementInterface $element
     * @return string
     * @throws \Zend\Form\Exception\DomainException
     */
    public function render(ElementInterface $element)
    {
        $renderer = $this->getView();
        if (!$renderer instanceof Pluggable) {
            // Bail early if renderer is not pluggable
            return '';
        }

        $labelHelper = $renderer->plugin('form_label');
        $elementHelper = $renderer->plugin('form_element');
        $elementErrorsHelper = $renderer->plugin('form_element_errors');

        $labelOpen      = $labelHelper->openTag();
        $labelClose     = $labelHelper->closeTag();
        $label = $element->getAttribute('label');
        $elementString = $elementHelper->render($element);
        $elementErrors = $elementErrorsHelper->render($element);

        if (!empty($label)) {
            switch ($this->labelPosition) {
                case self::LABEL_PREPEND:
                    $markup = $labelOpen . $label . $elementString . $labelClose . $elementErrors;
                    break;
                case self::LABEL_APPEND:
                default:
                    $markup = $labelOpen . $elementString . $label . $labelClose . $elementErrors;
                    break;
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
     * @param  ElementInterface|null $element
     * @return string
     */
    public function __invoke(ElementInterface $element = null)
    {
        if (!$element) {
            return $this;
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
}
