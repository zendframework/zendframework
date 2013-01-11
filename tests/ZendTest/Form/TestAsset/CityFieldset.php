<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Form\TestAsset;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class CityFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('city');
        $this->setHydrator(new ClassMethodsHydrator(false))
             ->setObject(new Entity\City());

        $name = new \Zend\Form\Element('name', array('label' => 'Name of the city'));
        $name->setAttribute('type', 'text');

        $zipCode = new \Zend\Form\Element('zipCode', array('label' => 'ZipCode of the city'));
        $zipCode->setAttribute('type', 'text');

        $country = new CountryFieldset;
        $country->setLabel('Country');

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
