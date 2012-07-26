<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace Zend\Form\Element;

use Zend\Form\Element;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\Date as DateValidator;
use Zend\Validator\DateStep as DateStepValidator;
use Zend\Validator\ValidatorInterface;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 */
class Time extends DateTime
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'time',
    );

    /**
     * Retrieves a Date Validator configured for a DateTime Input type
     *
     * @return ValidatorInterface
     */
    protected function getDateValidator()
    {
        return new DateValidator(array('format' => 'H:i:s'));
    }

    /**
     * Retrieves a DateStepValidator configured for a Date Input type
     *
     * @return ValidatorInterface
     */
    protected function getStepValidator()
    {
        $stepValue = (isset($this->attributes['step']))
                     ? $this->attributes['step'] : 60; // Seconds

        $baseValue = (isset($this->attributes['min']))
                     ? $this->attributes['min'] : '00:00:00';

        return new DateStepValidator(array(
            'format'    => 'H:i:s',
            'baseValue' => $baseValue,
            'step'      => new \DateInterval("PT{$stepValue}S"),
        ));
    }
}
