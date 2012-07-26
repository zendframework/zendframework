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

use DateInterval;
use DateTime as PhpDateTime;
use Zend\Form\Element;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\Date as DateValidator;
use Zend\Validator\DateStep as DateStepValidator;
use Zend\Validator\GreaterThan as GreaterThanValidator;
use Zend\Validator\LessThan as LessThanValidator;
use Zend\Validator\ValidatorInterface;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 */
class DateTime extends Element implements InputProviderInterface
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'datetime',
    );

    /**
     * Date format to use for DateTime values. By default, this is RFC-3339,
     * which is what HTML5 dictates.
     *
     * @var string
     */
    protected $format = PhpDateTime::RFC3339;

    /**
     * @var array
     */
    protected $validators;

    /**
     * Retrieve the element value
     *
     * If the value is a DateTime object, and $returnFormattedValue is true
     * (the default), we return the string
     * representation using the currently registered format.
     *
     * If $returnFormattedValue is false, the original value will be
     * returned, regardless of type.
     *
     * @param  bool $returnFormattedValue
     * @return mixed
     */
    public function getValue($returnFormattedValue = true)
    {
        $value = parent::getValue();
        if (!$value instanceof PhpDateTime || !$returnFormattedValue) {
            return $value;
        }
        $format = $this->getFormat();
        return $value->format($format);
    }

    /**
     * Set value for format
     *
     * @param  string format
     * @return DateTime
     */
    public function setFormat($format)
    {
        $this->format = (string) $format;
        return $this;
    }

    /**
     * Retrieve the DateTime format to use for the value
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Get validators
     *
     * @return array
     */
    protected function getValidators()
    {
        if ($this->validators) {
            return $this->validators;
        }

        $validators = array();
        $validators[] = $this->getDateValidator();

        if (isset($this->attributes['min'])) {
            $validators[] = new GreaterThanValidator(array(
                'min'       => $this->attributes['min'],
                'inclusive' => true,
            ));
        }
        if (isset($this->attributes['max'])) {
            $validators[] = new LessThanValidator(array(
                'max'       => $this->attributes['max'],
                'inclusive' => true,
            ));
        }
        if (!isset($this->attributes['step'])
            || 'any' !== $this->attributes['step']
        ) {
            $validators[] = $this->getStepValidator();
        }

        $this->validators = $validators;
        return $this->validators;
    }

    /**
     * Retrieves a Date Validator configured for a DateTime Input type
     *
     * @return DateTime
     */
    protected function getDateValidator()
    {
        return new DateValidator(array('format' => PhpDateTime::ISO8601));
    }

    /**
     * Retrieves a DateStep Validator configured for a DateTime Input type
     *
     * @return DateTime
     */
    protected function getStepValidator()
    {
        $stepValue = (isset($this->attributes['step']))
                     ? $this->attributes['step'] : 1; // Minutes

        $baseValue = (isset($this->attributes['min']))
                     ? $this->attributes['min'] : '1970-01-01T00:00:00Z';

        return new DateStepValidator(array(
            'format'    => PhpDateTime::ISO8601,
            'baseValue' => $baseValue,
            'step'      => new DateInterval("PT{$stepValue}M"),
        ));
    }

    /**
     * Provide default input rules for this element
     *
     * Attaches default validators for the datetime input.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        return array(
            'name' => $this->getName(),
            'required' => true,
            'filters' => array(
                array('name' => 'Zend\Filter\StringTrim'),
            ),
            'validators' => $this->getValidators(),
        );
    }
}
