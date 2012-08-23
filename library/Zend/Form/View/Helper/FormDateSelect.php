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

use DateTime;
use IntlDateFormatter;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;
use Zend\Form\View\Helper\FormMonthSelect as FormMonthSelectHelper;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 */
class FormDateSelect extends FormMonthSelectHelper
{
    /**
     * Render a date element that is composed of three selects
     *
     * @param  ElementInterface $element
     * @throws Exception\DomainException
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

        $daysOptions   = $this->getDaysOptions($pattern['day']);
        $monthsOptions = $this->getMonthsOptions($pattern['month']);
        $yearOptions   = $this->getYearsOptions($element->getMinYear(), $element->getMaxYear());

        $dayElement   = $element->getDayElement()->setAttribute('options', $daysOptions);
        $monthElement = $element->getMonthElement()->setAttribute('options', $monthsOptions);
        $yearElement  = $element->getYearElement()->setAttribute('options', $yearOptions);

        $markup = array();
        $markup[$pattern['day']]   = $selectHelper->render($dayElement);
        $markup[$pattern['month']] = $selectHelper->render($monthElement);
        $markup[$pattern['year']]  = $selectHelper->render($yearElement);

        $markup = sprintf(
            '%s %s %s %s %s',
            $markup[array_shift($pattern)],
            array_shift($pattern), // Delimiter
            $markup[array_shift($pattern)],
            array_shift($pattern), // Delimiter
            $markup[array_shift($pattern)]
        );

        return $markup;
    }

    /**
     * Create a key => value options for days
     *
     * @param string  $pattern Pattern to use for days
     * @return array
     */
    public function getDaysOptions($pattern)
    {
        $keyFormatter   = new IntlDateFormatter($this->getLocale(), null, null, null, null, 'dd');
        $valueFormatter = new IntlDateFormatter($this->getLocale(), null, null, null, null, $pattern);
        $date           = new DateTime('1970-01-01');

        $result = array();
        for ($day = 1; $day <= 31; $day++) {
            $key   = $keyFormatter->format($date);
            $value = $valueFormatter->format($date);
            $result[$key] = $value;

            $date->modify('+1 day');
        }

        return $result;
    }
}

