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

use Zend\Validator\ValidatorInterface;
use Zend\Validator\Regex as RegexValidator;

class MonthSelect extends DateSelect
{
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

