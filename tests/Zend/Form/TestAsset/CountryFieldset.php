<?php

namespace ZendTest\Form\TestAsset;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class CountryFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('country');
        $this->setHydrator(new ClassMethodsHydrator())
             ->setObject(new Entity\Country());

        $name = new \Zend\Form\Element('name', array('label' => 'Name of the country'));
        $name->setAttribute('type', 'text');

        $continent = new \Zend\Form\Element('continent', array('label' => 'Continent of the city'));
        $continent->setAttribute('type', 'text');

        $this->add($name);
        $this->add($continent);
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
            'continent' => array(
                'required' => true
            )
        );
    }
}
