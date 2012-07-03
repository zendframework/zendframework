<?php

namespace ZendTest\Form\TestAsset;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class CityFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('city');
        $this->setHydrator(new ClassMethodsHydrator())
             ->setObject(new Entity\City());

        $name = new \Zend\Form\Element('name');
        $name->setAttributes(array(
            'type' => 'text',
            'label' => 'Name of the city'
        ));

        $zipCode = new \Zend\Form\Element('zipCode');
        $zipCode->setAttributes(array(
            'type' => 'text',
            'label' => 'ZipCode of the city'
        ));

        $country = new CountryFieldset;
        $country->setAttributes(array(
            'label' => 'Pays'
        ));

        $this->add($name);
        $this->add($zipCode);
        $this->add($country);
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
            'name' => array(
                'required' => true,
            ),
            'zipCode' => array(
                'required' => true
            )
        );
    }
}