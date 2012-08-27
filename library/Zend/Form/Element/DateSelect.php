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

use DateTime;
use Zend\Form\Form;
use Zend\Validator\ValidatorInterface;
use Zend\Validator\Date as DateValidator;

class DateSelect extends MonthSelect
{
    /**
     * Select form element that contains values for day
     *
     * @var Select
     */
    protected $dayElement;

    /**
     * Constructor. Add the day select element
     *
     * @param  null|int|string  $name    Optional name for the element
     * @param  array            $options Optional options for the element
     */
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->dayElement = new Select('day');
    }

    /**
     * @return Select
     */
    public function getDayElement()
    {
        return $this->dayElement;
    }

    /**
     * @param mixed $value
     * @return void|\Zend\Form\Element
     */
    public function setValue($value)
    {
        parent::setValue($value);
        $this->dayElement->setValue($value['day']);
    }

    /**
     * Prepare the form element (mostly used for rendering purposes)
     *
     * @param Form $form
     * @return mixed
     */
    public function prepareElement(Form $form)
    {
        parent::prepareElement($form);

        $name = $this->getName();
        $this->dayElement->setName($name . '[day]');
    }

    /**
     * Get validator
     *
     * @return ValidatorInterface
     */
    protected function getValidator()
    {
        if (null === $this->validator) {
            $this->validator = new DateValidator(array('format' => 'Y-m-d'));
        }

        return $this->validator;
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
                                $date = $date['year'] . '-' . $date['month'] . '-' . $date['day'];
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

