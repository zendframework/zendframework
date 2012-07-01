<?php

namespace ZendTest\Form\TestAsset;

use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class CreateAddressForm extends Form
{
    public function __construct()
    {
        parent::__construct('create_address');

        $this->setAttribute('method', 'post')
             ->setHydrator(new ClassMethodsHydrator())
             ->setInputFilter(new InputFilter());

        $address = new AddressFieldset();
        $address->setUseAsBaseFieldset(true);
        $this->add($address);

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit'
            )
        ));
    }
}
