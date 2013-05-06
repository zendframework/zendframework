<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form\TestAsset;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\Validator;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class CustomForm extends Form
{
    public function __construct()
    {
        parent::__construct('test_form');

        $this->setAttribute('method', 'post')
             ->setHydrator(new ClassMethodsHydrator());

        $field1 = new Element('name', array('label' => 'Name'));
        $field1->setAttribute('type', 'text');
        $this->add($field1);

        $field2 = new Element('email', array('label' => 'Email'));
        $field2->setAttribute('type', 'text');
        $this->add($field2);

        $this->add(array(
            'name' => 'csrf',
            'type' => 'Zend\Form\Element\Csrf',
            'attributes' => array(
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit'
            )
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'name' => array(
                'required' => true,
                'filters'  => array(
                    array('name' => 'Zend\Filter\StringTrim'),
                ),
            ),
            'email' => array(
                'required' => true,
                'filters'  => array(
                    array('name' => 'Zend\Filter\StringTrim'),
                ),
                'validators' => array(
                    new Validator\EmailAddress(),
                ),
            ),
        );
    }
}
