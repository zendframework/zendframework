<?php

namespace ZendTest\Form\TestAsset;

use Zend\Filter\Filter,
    Zend\Form\Element,
    Zend\Form\Form;

class ArrayFilter implements Filter
{
    public function filter($value)
    {
        $value = array_filter($value, array($this, '_filter'));
        return $value;
    }

    protected function _filter($value)
    {
        if (is_array($value)) {
            return array_filter($value, array($this, '_filter'));
        }
        return (strstr($value, 'ba'));
    }

    /**
     * Check array notation for validators
     */
    public function testValidatorsGivenArrayKeysOnValidation()
    {
        $username = new Element('username');
        $username->addValidator('stringLength', true, array('min' => 5, 'max' => 20, 'ignore' => 'something'));
        $form = new Form(array('elements' => array($username)));
        $this->assertTrue($form->isValid(array('username' => 'abcde')));
    }
}
