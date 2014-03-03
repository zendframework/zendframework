<?php

namespace ZendTest\Mvc\Controller\Plugin\TestAsset;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class TestFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        $this->add(array(
            'name' => 'text',
            'type' => 'text',
        ));

        $this->add(array(
            'name' => 'file',
            'type' => 'file',
        ));

    }

    public function getInputFilterSpecification()
    {
        return array(
            'text' => array(
                'required' => true,
            ),
            'file' => array(
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'filerenameupload',
                        'options' => array(
                            'target'    => __DIR__ . '/testfile.jpg',
                            'overwrite' => true,
                        )
                    )
                ),
            ),
        );
    }
}
