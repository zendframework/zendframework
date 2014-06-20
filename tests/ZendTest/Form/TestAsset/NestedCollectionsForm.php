<?php
namespace ZendTest\Form\TestAsset;

use Zend\Form\Form;
use Zend\Form\Fieldset;

class NestedCollectionsForm extends Form
{   

    public function __construct()
    {
        parent::__construct('nestedCollectionsForm');
        
        $this->add(array(
            'name' => 'testFieldset',
            'type' => 'Zend\Form\Fieldset',
            'options' => array(
                 'use_as_base_fieldset' => true,
             ),
            'elements' => array(
                array(
                    'spec' => array(
                        'name' => 'groups',
                        'type' => 'Zend\Form\Element\Collection',
                        'options' => array(
                            'target_element' => array(
                                'type' => 'Zend\Form\Fieldset',
                                'name' => 'group',
                                'elements' => array(
                                    array(
                                        'spec' => array(
                                            'type' => 'Zend\Form\Element\Text',
                                            'name' => 'name',
                                        ),
                                    ),
                                    array(
                                        'spec' => array(
                                            'type' => 'Zend\Form\Element\Collection',
                                            'name' => 'items',
                                            'options' => array(
                                                'target_element' => array(
                                                    'type' => 'Zend\Form\Fieldset',
                                                    'name' => 'item',
                                                    'elements' => array(
                                                        array(
                                                            'spec' => array(
                                                                'type' => 'Zend\Form\Element\Text',
                                                                'name' => 'itemId',
                                                            ),
                                                        ),
                                                    ),
                                                ),
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ));
        

        $this->setValidationGroup(array(
            'testFieldset' => array(
                'groups' => array(
                    'name',
                    'items' => array(
                        'itemId'
                    )
                ),
            )
        ));
    }


}
