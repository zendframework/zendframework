<?php

namespace ZendTest\Mvc\Controller\Plugin\TestAsset;


use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class LinksFieldset extends Fieldset implements  InputFilterProviderInterface{

    public function __construct()
    {
        parent::__construct('link');
        $this->add(array(
            'name' => 'foobar',
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'email' => array(
                'required' => false,
            ),
        );
    }
}