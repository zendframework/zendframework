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
use Zend\Form\ElementPrepareAwareInterface;
use Zend\Form\Form;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\ValidatorInterface;
use Zend\Validator\Regex as RegexValidator;

class MonthSelect extends Element implements InputProviderInterface, ElementPrepareAwareInterface
{
    /**
     * Select form element that contains values for month
     *
     * @var Select
     */
    protected $monthElement;

    /**
     * Select form element that contains values for year
     *
     * @var Select
     */
    protected $yearElement;

    /**
     * Min year to use for the select (default: current year - 100)
     *
     * @var int
     */
    protected $minYear;

    /**
     * Max year to use for the select (default: current year)
     *
     * @var int
     */
    protected $maxYear;

    /**
     * @var ValidatorInterface
     */
    protected $validator;


    /**
     * Constructor. Add two selects elements
     *
     * @param  null|int|string  $name    Optional name for the element
     * @param  array            $options Optional options for the element
     */
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->monthElement = new Select('month');
        $this->yearElement = new Select('year');

        $this->maxYear = date('Y');
        $this->minYear = $this->maxYear - 100;
    }

    /**
     * Accepted options for DateSelect:
     * - min_year: min year to use in the year select
     * - max_year: max year to use in the year select
     *
     * @param array|\Traversable $options
     * @return DateSelect
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($options['min_year'])) {
            $this->setMinYear($options['min_year']);
        }

        if (isset($options['max_year'])) {
            $this->setMaxYear($options['max_year']);
        }

        return $this;
    }

    /**
     * @return Select
     */
    public function getMonthElement()
    {
        return $this->monthElement;
    }

    /**
     * @return Select
     */
    public function getYearElement()
    {
        return $this->yearElement;
    }

    /**
     * @param  int $minYear
     * @return DateSelect
     */
    public function setMinYear($minYear)
    {
        $this->minYear = $minYear;
        return $this;
    }

    /**
     * @return int
     */
    public function getMinYear()
    {
        return $this->minYear;
    }

    /**
     * @param  int $maxYear
     * @return DateSelect
     */
    public function setMaxYear($maxYear)
    {
        $this->maxYear = $maxYear;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxYear()
    {
        return $this->maxYear;
    }

    /**
     * @param mixed $value
     * @return void|\Zend\Form\Element
     */
    public function setValue($value)
    {
        $this->monthElement->setValue($value['month']);
        $this->yearElement->setValue($value['year']);
    }

    /**
     * Prepare the form element (mostly used for rendering purposes)
     *
     * @param Form $form
     * @return mixed
     */
    public function prepareElement(Form $form)
    {
        $name = $this->getName();
        $this->monthElement->setName($name . '[month]');
        $this->yearElement->setName($name . '[year]');
    }

    /**
     * Get validator
     *
     * @return ValidatorInterface
     */
    protected function getValidator()
    {
        return new RegexValidator('/^[0-9]{4}\-(0?[1-9]|1[012])$/');
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInput()}.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        return array(
            'name' => $this->getName(),
            'required' => false,
            'filters' => array(
                array(
                    'name'    => 'Callback',
                    'options' => array(
                        'callback' => function($date) {
                            // Convert the date to a specific format
                            if (is_array($date)) {
                                $date = $date['year'] . '-' . $date['month'];
                            }

                            return $date;
                        }
                    )
                )
            ),
            'validators' => array(
                $this->getValidator(),
            )
        );
    }
}

