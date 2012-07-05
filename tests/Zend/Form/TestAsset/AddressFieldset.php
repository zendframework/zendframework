<?php

namespace ZendTest\Form\TestAsset;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class AddressFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('address');
        $this->setHydrator(new ClassMethodsHydrator())
             ->setObject(new Entity\Address());

        $street = new \Zend\Form\Element('street', array('label' => 'Street'));
        $street->setAttribute('type', 'text');

        $city = new CityFieldset;
        $city->setLabel('City');

        $this->add($street);
        $this->add($city);
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            'street' => array(
                'required' => true,
            )
        );
    }
}
