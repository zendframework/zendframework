<?php

namespace ZendTest\Form\TestAsset;

use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class NewProductForm extends Form
{
    public function __construct()
    {
        parent::__construct('create_product');

        $this->setAttribute('method', 'post')
             ->setHydrator(new ClassMethodsHydrator())
             ->setInputFilter(new InputFilter());

        $fieldset = new ProductFieldset();
        $fieldset->setUseAsBaseFieldset(true);
        $this->add($fieldset);

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit'
            )
        ));
    }
}
