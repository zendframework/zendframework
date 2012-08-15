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

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormDateSelect as FormDateSelectHelper;
use Zend\Form\Exception;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 */
class FormMonthSelect extends FormDateSelectHelper
{
    /**
     * Render a month element that is composed of two selects
     *
     * @param \Zend\Form\ElementInterface $element
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $name = $element->getName();
        if ($name === null || $name === '') {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $selectHelper = $this->getSelectElementHelper();
        $pattern      = $this->parsePattern();

        // The pattern always contains "day" part and the first separator, so we have to remove it
        unset($pattern['day']);
        unset($pattern[0]);

        $monthsOptions = $this->getMonthsOptions($pattern['month']);
        $yearOptions   = $this->getYearsOptions($element->getMinYear(), $element->getMaxYear());

        $monthElement = $element->getMonthElement()->setAttribute('options', $monthsOptions);
        $yearElement  = $element->getYearElement()->setAttribute('options', $yearOptions);

        $markup = array();
        $markup[$pattern['month']] = $selectHelper->render($monthElement);
        $markup[$pattern['year']]  = $selectHelper->render($yearElement);

        $markup = sprintf(
            '%s %s %s',
            $markup[array_shift($pattern)],
            array_shift($pattern), // Delimiter
            $markup[array_shift($pattern)]
        );

        return $markup;
    }
}

