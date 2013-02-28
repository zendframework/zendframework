<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Form\Element;

use Zend\Form\Element;
use Zend\Validator\DateStep as DateStepValidator;

class DateTimeLocal extends DateTime
{
    const DATETIME_LOCAL_FORMAT = 'Y-m-d\TH:i';

    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'datetime-local',
    );

    /**
     *
     * Opera and mobile browsers support datetime input, and display a datepicker control
     * But the submitted value does not include seconds.
     *
     * @var string
     */
    protected $format = self::DATETIME_LOCAL_FORMAT;

    /**
     * Retrieves a DateStepValidator configured for a Date Input type
     *
     * @return \Zend\Validator\ValidatorInterface
     */
    protected function getStepValidator()
    {
        $stepValue = (isset($this->attributes['step']))
                     ? $this->attributes['step'] : 1; // Minutes

        $baseValue = (isset($this->attributes['min']))
                     ? $this->attributes['min'] : '1970-01-01T00:00';

        return new DateStepValidator(array(
            'format'    => $this->format,
            'baseValue' => $baseValue,
            'step'      => new \DateInterval("PT{$stepValue}M"),
        ));
    }
}
